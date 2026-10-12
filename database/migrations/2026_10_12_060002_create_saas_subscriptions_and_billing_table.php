<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price_monthly', 12, 2)->default(0);
            $table->decimal('price_yearly', 12, 2)->default(0);
            $table->integer('max_branches')->default(1);
            $table->integer('max_users')->default(2);
            $table->integer('max_products')->default(1000);
            $table->integer('max_warehouses')->default(1);
            $table->integer('max_storage_mb')->default(1024);
            $table->integer('sms_limit')->default(100);
            $table->json('features')->nullable(); // array of enabled features
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('addons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type'); // extra_branch, extra_user, extra_warehouse, extra_sms, extra_storage
            $table->decimal('price_monthly', 12, 2)->default(0);
            $table->decimal('price_yearly', 12, 2)->default(0);
            $table->integer('unit_value')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('subscription_plans')->onDelete('cascade');
            $table->string('billing_cycle')->default('monthly'); // monthly, quarterly, yearly
            $table->string('status')->default('trial'); // trial, active, renewal_due, grace_period, suspended, cancelled, expired
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('grace_ends_at')->nullable();
            $table->boolean('auto_renew')->default(false);
            $table->timestamps();
        });

        Schema::create('subscription_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('subscriptions')->onDelete('cascade');
            $table->foreignId('addon_id')->constrained('addons')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->decimal('price', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('discount_type')->default('percent'); // percent, fixed
            $table->decimal('discount_value', 12, 2);
            $table->decimal('max_discount', 12, 2)->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('used_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('subscription_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('subscription_id')->constrained('subscriptions')->onDelete('cascade');
            $table->string('invoice_no')->unique();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('status')->default('unpaid'); // unpaid, paid, refunded, failed
            $table->date('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade');
            $table->nullableMorphs('payable'); // subscription_invoice or sale
            $table->string('gateway'); // bkash, nagad, rocket, sslcommerz, bank, cash
            $table->string('transaction_id')->nullable()->index();
            $table->decimal('amount', 12, 2);
            $table->string('currency')->default('BDT');
            $table->string('status')->default('pending'); // pending, completed, failed, refunded
            $table->json('gateway_response')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('subscription_invoices');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('subscription_addons');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('addons');
        Schema::dropIfExists('subscription_plans');
    }
};
