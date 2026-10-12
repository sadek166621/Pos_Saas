<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'max_discount',
        'valid_until',
        'usage_limit',
        'used_count',
        'is_active',
    ];

    protected $casts = [
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
        'discount_value' => 'decimal:2',
        'max_discount' => 'decimal:2',
    ];

    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->valid_until && $this->valid_until->isPast()) {
            return false;
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }
}
