<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Sale;
use App\Services\PosService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class SyncApiController extends Controller
{
    public function __construct(
        protected PosService $posService
    ) {}

    /**
     * Sync offline sales batch.
     */
    public function syncOfflineSales(Request $request): JsonResponse
    {
        $user = $request->user();
        $tenant = $user->tenant;
        $branchId = $request->input('branch_id', $user->branch_id);
        $branch = Branch::where('tenant_id', $tenant->id)->findOrFail($branchId);

        $salesBatch = $request->input('sales', []);
        $synced = [];
        $failed = [];

        foreach ($salesBatch as $saleData) {
            $offlineId = $saleData['offline_id'] ?? null;

            // Check if already synced
            if ($offlineId && Sale::where('tenant_id', $tenant->id)->where('notes', 'LIKE', "%OfflineID:{$offlineId}%")->exists()) {
                $synced[] = [
                    'offline_id' => $offlineId,
                    'status' => 'already_synced',
                ];
                continue;
            }

            try {
                $saleData['notes'] = ($saleData['notes'] ?? '') . " [OfflineID:{$offlineId}]";
                $sale = $this->posService->processSale($tenant, $branch, $saleData, $user->id);

                $synced[] = [
                    'offline_id' => $offlineId,
                    'server_id' => $sale->id,
                    'invoice_no' => $sale->invoice_no,
                    'status' => 'synced',
                ];
            } catch (Exception $e) {
                $failed[] = [
                    'offline_id' => $offlineId,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Sync batch processed',
            'synced_count' => count($synced),
            'failed_count' => count($failed),
            'synced' => $synced,
            'failed' => $failed,
        ]);
    }
}
