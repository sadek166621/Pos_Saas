<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransferItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_id',
        'product_id',
        'quantity_transferred',
        'quantity_received',
        'unit_cost',
    ];

    protected $casts = [
        'quantity_transferred' => 'decimal:2',
        'quantity_received' => 'decimal:2',
        'unit_cost' => 'decimal:2',
    ];

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(StockTransfer::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
