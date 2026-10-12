<?php

namespace App\Services;

use App\Models\PaymentTransaction;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;

class PaymentGatewayService
{
    /**
     * Initiate a payment through selected gateway.
     */
    public function initiatePayment(Tenant $tenant, string $gateway, float $amount, $payable, array $extra = []): array
    {
        $trxId = strtoupper($gateway) . '-' . date('YmdHis') . '-' . rand(1000, 9999);

        $transaction = PaymentTransaction::create([
            'tenant_id' => $tenant->id,
            'payable_type' => get_class($payable),
            'payable_id' => $payable->id,
            'gateway' => $gateway,
            'transaction_id' => $trxId,
            'amount' => $amount,
            'currency' => 'BDT',
            'status' => 'pending',
            'gateway_response' => [
                'initiated_at' => now()->toIso8601String(),
                'gateway_ref' => uniqid('GW_'),
                'extra' => $extra,
            ],
            'notes' => "Payment initiated via {$gateway}",
        ]);

        return [
            'success' => true,
            'transaction_id' => $trxId,
            'gateway' => $gateway,
            'amount' => $amount,
            'payment_url' => route('subscription.payment.mock-callback', ['trx' => $trxId]),
        ];
    }

    /**
     * Complete or verify transaction (e.g. from bKash execute or webhook).
     */
    public function verifyPayment(string $transactionId, array $gatewayPayload = []): array
    {
        $transaction = PaymentTransaction::where('transaction_id', $transactionId)->first();

        if (!$transaction) {
            return ['success' => false, 'message' => 'Transaction not found.'];
        }

        $transaction->update([
            'status' => 'completed',
            'gateway_response' => array_merge($transaction->gateway_response ?? [], [
                'verified_at' => now()->toIso8601String(),
                'payload' => $gatewayPayload,
            ]),
        ]);

        // If payable has update status
        if ($transaction->payable && method_exists($transaction->payable, 'update')) {
            $transaction->payable->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
        }

        Log::info("Payment verified successfully for transaction: {$transactionId}");

        return [
            'success' => true,
            'transaction' => $transaction,
        ];
    }
}
