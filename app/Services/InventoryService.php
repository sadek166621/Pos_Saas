<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\BranchProductStock;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class InventoryService
{
    /**
     * Transfer stock between branches.
     */
    public function transferStock(Tenant $tenant, int $fromBranchId, int $toBranchId, array $items, int $userId, ?string $notes = null): StockTransfer
    {
        return DB::transaction(function () use ($tenant, $fromBranchId, $toBranchId, $items, $userId, $notes) {
            if ($fromBranchId === $toBranchId) {
                throw new Exception('Source and destination branches cannot be the same.');
            }

            $transferNo = 'TRF-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $transfer = StockTransfer::create([
                'tenant_id' => $tenant->id,
                'from_branch_id' => $fromBranchId,
                'to_branch_id' => $toBranchId,
                'transfer_no' => $transferNo,
                'status' => 'received', // Instant or pending based on workflow
                'transferred_by' => $userId,
                'received_by' => $userId,
                'transfer_date' => Carbon::now('Asia/Dhaka')->toDateString(),
                'notes' => $notes,
            ]);

            foreach ($items as $item) {
                $productId = $item['product_id'];
                $qty = (float) $item['quantity'];

                $product = Product::where('tenant_id', $tenant->id)->findOrFail($productId);

                // Deduct from sender branch
                $fromStock = BranchProductStock::firstOrCreate([
                    'tenant_id' => $tenant->id,
                    'branch_id' => $fromBranchId,
                    'product_id' => $productId,
                ], ['quantity' => 0]);

                $fromStock->decrement('quantity', $qty);

                // Add to receiver branch
                $toStock = BranchProductStock::firstOrCreate([
                    'tenant_id' => $tenant->id,
                    'branch_id' => $toBranchId,
                    'product_id' => $productId,
                ], ['quantity' => 0]);

                $toStock->increment('quantity', $qty);

                // Record transfer item
                StockTransferItem::create([
                    'transfer_id' => $transfer->id,
                    'product_id' => $productId,
                    'quantity_transferred' => $qty,
                    'quantity_received' => $qty,
                    'unit_cost' => (float) $product->purchase_price,
                ]);

                // Record movements
                StockMovement::create([
                    'tenant_id' => $tenant->id,
                    'branch_id' => $fromBranchId,
                    'product_id' => $productId,
                    'type' => 'transfer_out',
                    'quantity' => -$qty,
                    'unit_cost' => (float) $product->purchase_price,
                    'reference_type' => StockTransfer::class,
                    'reference_id' => $transfer->id,
                    'notes' => 'Transferred out to branch #' . $toBranchId,
                    'created_by' => $userId,
                ]);

                StockMovement::create([
                    'tenant_id' => $tenant->id,
                    'branch_id' => $toBranchId,
                    'product_id' => $productId,
                    'type' => 'transfer_in',
                    'quantity' => $qty,
                    'unit_cost' => (float) $product->purchase_price,
                    'reference_type' => StockTransfer::class,
                    'reference_id' => $transfer->id,
                    'notes' => 'Transferred in from branch #' . $fromBranchId,
                    'created_by' => $userId,
                ]);
            }

            return $transfer;
        });
    }

    /**
     * Adjust stock (Damage, Loss, or physical audit correction).
     */
    public function adjustStock(Tenant $tenant, int $branchId, string $type, array $items, int $userId, ?string $notes = null): StockAdjustment
    {
        return DB::transaction(function () use ($tenant, $branchId, $type, $items, $userId, $notes) {
            $adjNo = 'ADJ-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $adjustment = StockAdjustment::create([
                'tenant_id' => $tenant->id,
                'branch_id' => $branchId,
                'adjustment_no' => $adjNo,
                'type' => $type, // addition, subtraction, damage
                'notes' => $notes,
                'created_by' => $userId,
            ]);

            foreach ($items as $item) {
                $productId = $item['product_id'];
                $qty = (float) $item['quantity'];
                $product = Product::where('tenant_id', $tenant->id)->findOrFail($productId);

                $stock = BranchProductStock::firstOrCreate([
                    'tenant_id' => $tenant->id,
                    'branch_id' => $branchId,
                    'product_id' => $productId,
                ], ['quantity' => 0]);

                if ($type === 'addition') {
                    $stock->increment('quantity', $qty);
                    $movementQty = $qty;
                } else {
                    $stock->decrement('quantity', $qty);
                    $movementQty = -$qty;
                }

                StockAdjustmentItem::create([
                    'adjustment_id' => $adjustment->id,
                    'product_id' => $productId,
                    'quantity' => $qty,
                    'unit_cost' => (float) $product->purchase_price,
                ]);

                StockMovement::create([
                    'tenant_id' => $tenant->id,
                    'branch_id' => $branchId,
                    'product_id' => $productId,
                    'type' => 'adjustment',
                    'quantity' => $movementQty,
                    'unit_cost' => (float) $product->purchase_price,
                    'reference_type' => StockAdjustment::class,
                    'reference_id' => $adjustment->id,
                    'notes' => ucfirst($type) . ' adjustment: ' . $adjNo,
                    'created_by' => $userId,
                ]);
            }

            return $adjustment;
        });
    }
}
