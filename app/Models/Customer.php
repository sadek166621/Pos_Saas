<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'phone',
        'email',
        'address',
        'city',
        'type',
        'credit_limit',
        'previous_due',
        'current_due',
        'loyalty_points',
        'status',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'previous_due' => 'decimal:2',
        'current_due' => 'decimal:2',
        'loyalty_points' => 'integer',
        'status' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function ledgers(): HasMany
    {
        return $this->hasMany(CustomerLedger::class);
    }
}
