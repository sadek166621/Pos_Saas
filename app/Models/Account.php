<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'type',
        'account_number',
        'bank_name',
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(AccountTransaction::class);
    }
}
