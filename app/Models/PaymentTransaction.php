<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'payable_type',
        'payable_id',
        'gateway',
        'transaction_id',
        'amount',
        'currency',
        'status',
        'gateway_response',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }
}
