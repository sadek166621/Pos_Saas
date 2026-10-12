<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\BranchProductStock;
use App\Models\CashShift;
use App\Models\Customer;
use App\Models\CustomerLedger;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\StockMovement;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class PosService
{
    /**
     * Process and record a new sale from the POS terminal.
     */
    public function processSale(Tenant $tenant, Branch $branch, array $data, int $cashierId): Sale
    {
        return DB::transaction(function () use ($tenant, $branch, $data, $cashierId) {
            $customerId = $data['customer_id'] ?? null;
            $items = $data['items'] ?? [];

            if (empty($items)) {
                throw new Exception('Cart is empty. Please add items to checkout.');
            }

            // Generate Invoice No
            $today = date('Ymd');
            $latestSale = Sale::where('tenant_id', $tenant->id)
                ->whereDate('created_at', Carbon::today())
                ->latest('id')
                ->first();

            $nextSeq = $latestSale ? ((int) substr($latestSale->invoice_no, -4)) + 1 : 1;
            $invoiceNo = 'INV-' . $today . '-' . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);

            // Active Shift
            $shift = CashShift::where('tenant_id', $tenant->id)
                ->where('branch_id', $branch->id)
                ->where('status', 'open')
                ->latest('id')
                ->first();

            // Calculate Subtotal and Cost
            $subtotal = 0;
            $totalCost = 0;
            $saleItemsData = [];

            foreach ($items as $item) {
                $product = Product::where('tenant_id', $tenant->id)->findOrFail($item['product_id']);
                $qty = (float) $item['quantity'];
                $unitPrice = isset($item['unit_price']) ? (float) $item['unit_price'] : $product->getPriceForBranch($branch->id);
                $discount = (float) ($item['discount'] ?? 0);
                $vat = (float) ($item['vat'] ?? 0);
                $lineTotal = ($qty * $unitPrice) - $discount + $vat;

                $subtotal += ($qty * $unitPrice);
                $totalCost += ($qty * (float) $product->purchase_price);

                $saleItemsData[] = [
                    'product' => $product,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'cost_price' => (float) $product->purchase_price,
                    'discount' => $discount,
                    'vat' => $vat,
                    'subtotal' => $lineTotal,
                ];
            }

            $overallDiscount = (float) ($data['discount_amount'] ?? 0);
            $vatAmount = (float) ($data['vat_amount'] ?? 0);
            $serviceCharge = (float) ($data['service_charge'] ?? 0);
            $total = max(0, $subtotal - $overallDiscount + $vatAmount + $serviceCharge);

            $paid = (float) ($data['paid_amount'] ?? $total);
            $due = max(0, $total - $paid);
            $paymentStatus = $due <= 0 ? 'paid' : ($paid > 0 ? 'partial' : 'due');

            // 1. Create Sale Record
            $sale = Sale::create([
                'tenant_id' => $tenant->id,
                'branch_id' => $branch->id,
                'customer_id' => $customerId,
                'shift_id' => $shift ? $shift->id : null,
                'invoice_no' => $invoiceNo,
                'subtotal' => $subtotal,
                'discount_type' => $data['discount_type'] ?? 'fixed',
                'discount_amount' => $overallDiscount,
                'vat_amount' => $vatAmount,
                'service_charge' => $serviceCharge,
                'total' => $total,
                'paid' => $paid,
                'due' => $due,
                'payment_status' => $paymentStatus,
                'status' => 'completed',
                'payment_method' => $data['payment_method'] ?? 'cash',
                'sold_by' => $cashierId,
                'sale_date' => Carbon::now('Asia/Dhaka'),
                'notes' => $data['notes'] ?? null,
            ]);

            // 2. Insert Sale Items & Deduct Branch Stock
            foreach ($saleItemsData as $itemData) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $itemData['product']->id,
                    'product_name' => $itemData['product']->name,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'cost_price' => $itemData['cost_price'],
                    'discount' => $itemData['discount'],
                    'vat' => $itemData['vat'],
                    'subtotal' => $itemData['subtotal'],
                ]);

                // Deduct stock in branch
                $stockRecord = BranchProductStock::firstOrCreate([
                    'tenant_id' => $tenant->id,
                    'branch_id' => $branch->id,
                    'product_id' => $itemData['product']->id,
                ], ['quantity' => 0]);

                $stockRecord->decrement('quantity', $itemData['quantity']);

                // Record Stock Movement
                StockMovement::create([
                    'tenant_id' => $tenant->id,
                    'branch_id' => $branch->id,
                    'product_id' => $itemData['product']->id,
                    'type' => 'sale',
                    'quantity' => -$itemData['quantity'],
                    'unit_cost' => $itemData['cost_price'],
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'notes' => 'Sold on Invoice: ' . $invoiceNo,
                    'created_by' => $cashierId,
                ]);
            }

            // 3. Record Payments (Supports Split Payment)
            $payments = $data['payments'] ?? [];
            if (empty($payments) && $paid > 0) {
                $payments = [[
                    'method' => $data['payment_method'] ?? 'cash',
                    'amount' => $paid,
                    'trx_id' => $data['trx_id'] ?? null,
                ]];
            }

            $cashCollected = 0;
            foreach ($payments as $pIdx => $pay) {
                $payAmount = (float) ($pay['amount'] ?? 0);
                if ($payAmount <= 0) continue;

                $method = $pay['method'] ?? 'cash';
                if ($method === 'cash') {
                    $cashCollected += $payAmount;
                }

                SalePayment::create([
                    'tenant_id' => $tenant->id,
                    'sale_id' => $sale->id,
                    'customer_id' => $customerId,
                    'payment_no' => 'PAY-' . $sale->id . '-' . ($pIdx + 1),
                    'method' => $method,
                    'amount' => $payAmount,
                    'trx_id' => $pay['trx_id'] ?? null,
                    'paid_at' => Carbon::now('Asia/Dhaka'),
                    'notes' => $pay['notes'] ?? null,
                ]);
            }

            // 4. Update Shift Cash Sales
            if ($shift && $cashCollected > 0) {
                $shift->increment('cash_sales', $cashCollected);
            }

            // 5. Update Customer Due & Ledger if Due Exists
            if ($customerId && $due > 0) {
                $customer = Customer::where('tenant_id', $tenant->id)->find($customerId);
                if ($customer) {
                    $customer->increment('current_due', $due);

                    CustomerLedger::create([
                        'tenant_id' => $tenant->id,
                        'customer_id' => $customer->id,
                        'type' => 'sale',
                        'debit' => $due,
                        'balance' => $customer->current_due,
                        'reference_no' => $invoiceNo,
                        'notes' => 'Due for sale on invoice: ' . $invoiceNo,
                    ]);
                }
            }

            return $sale->load(['items.product', 'payments', 'customer', 'branch']);
        });
    }
}
