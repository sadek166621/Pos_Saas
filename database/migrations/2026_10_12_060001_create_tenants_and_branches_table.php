<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('business_type')->default('retail'); // grocery, fashion, electronics, pharmacy, wholesale, restaurant, other
            $table->string('bin_number')->nullable();
            $table->string('tin_number')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable()->default('Dhaka');
            $table->string('currency')->default('BDT');
            $table->string('currency_symbol')->default('৳');
            $table->string('timezone')->default('Asia/Dhaka');
            $table->string('logo')->nullable();
            $table->string('status')->default('trial'); // trial, active, renewal_due, grace_period, suspended, expired, cancelled
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('subscription_ends_at')->nullable();
            $table->timestamp('grace_ends_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tenant_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('key');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'key']);
        });

        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->default('Dhaka');
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->boolean('is_main')->default(false);
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade');
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('address')->nullable();
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('branches');
        Schema::dropIfExists('tenant_settings');
        Schema::dropIfExists('tenants');
    }
};
