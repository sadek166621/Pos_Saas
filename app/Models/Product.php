<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'category_id',
        'brand_id',
        'unit_id',
        'name',
        'slug',
        'sku',
        'barcode',
        'type',
        'purchase_price',
        'selling_price',
        'wholesale_price',
        'dealer_price',
        'min_stock_alert',
        'vat_percent',
        'image',
        'description',
        'status',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'dealer_price' => 'decimal:2',
        'vat_percent' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function branchPrices(): HasMany
    {
        return $this->hasMany(BranchProductPrice::class);
    }

    public function branchStocks(): HasMany
    {
        return $this->hasMany(BranchProductStock::class);
    }

    public function getPriceForBranch(int $branchId): float
    {
        $override = $this->branchPrices()->where('branch_id', $branchId)->first();
        if ($override && $override->selling_price > 0) {
            return (float) $override->selling_price;
        }
        return (float) $this->selling_price;
    }

    public function getStockForBranch(int $branchId): float
    {
        return (float) $this->branchStocks()->where('branch_id', $branchId)->sum('quantity');
    }
}
