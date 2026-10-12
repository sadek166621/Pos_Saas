<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'business_type',
        'bin_number',
        'tin_number',
        'phone',
        'email',
        'address',
        'city',
        'currency',
        'currency_symbol',
        'timezone',
        'logo',
        'status',
        'trial_ends_at',
        'subscription_ends_at',
        'grace_ends_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'grace_ends_at' => 'datetime',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function warehouses(): HasMany
    {
        return $this->hasMany(Warehouse::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    public function settings(): HasMany
    {
        return $this->hasMany(TenantSetting::class);
    }

    public function getSetting(string $key, $default = null)
    {
        $setting = $this->settings()->where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public function hasFeature(string $feature): bool
    {
        $sub = $this->activeSubscription;
        if (!$sub || !$sub->plan) {
            return true; // default in dev/trial
        }
        $features = $sub->plan->features ?? [];
        return in_array($feature, $features) || in_array('*', $features);
    }

    public function canCreateBranch(): bool
    {
        $sub = $this->activeSubscription;
        $max = $sub && $sub->plan ? $sub->plan->max_branches : 3;
        return $this->branches()->count() < $max;
    }

    public function canCreateUser(): bool
    {
        $sub = $this->activeSubscription;
        $max = $sub && $sub->plan ? $sub->plan->max_users : 5;
        return $this->users()->count() < $max;
    }

    public function canCreateProduct(): bool
    {
        $sub = $this->activeSubscription;
        $max = $sub && $sub->plan ? $sub->plan->max_products : 10000;
        return $this->products()->count() < $max;
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function isGracePeriod(): bool
    {
        return $this->status === 'grace_period' && $this->grace_ends_at && $this->grace_ends_at->isFuture();
    }
}
