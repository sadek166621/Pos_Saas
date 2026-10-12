<?php

use App\Http\Controllers\Api\PosApiController;
use App\Http\Controllers\Api\SyncApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user()->load(['tenant', 'branch']);
    });

    Route::get('/pos/products', [PosApiController::class, 'products']);
    Route::post('/pos/checkout', [PosApiController::class, 'checkout']);
    Route::post('/sync/offline-sales', [SyncApiController::class, 'syncOfflineSales']);
});

// Fallback public route for API health check
Route::get('/ping', function () {
    return response()->json([
        'status' => 'OK',
        'timestamp' => now()->toIso8601String(),
        'app' => config('app.name'),
    ]);
});
