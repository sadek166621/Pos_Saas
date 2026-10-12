<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'company_name',
        'phone',
        'email',
        'address',
        'city',
        'previous_due',
        'current_due',
        'status',
    ];

    protected $casts = [
        'previous_due' => 'decimal:2',
        'current_due' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function ledgers(): HasMany
    {
        return $this->hasMany(SupplierLedger::class);
    }
}
