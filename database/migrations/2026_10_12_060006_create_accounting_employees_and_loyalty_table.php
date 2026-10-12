<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('designation')->nullable(); // Manager, Cashier, Salesman
            $table->decimal('salary', 12, 2)->default(0);
            $table->decimal('commission_rate', 5, 2)->default(0); // percentage commission
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('expense_categories')->onDelete('cascade');
            $table->foreignId('shift_id')->nullable()->constrained('cash_shifts')->onDelete('set null');
            $table->string('expense_no')->unique();
            $table->string('title');
            $table->decimal('amount', 12, 2);
            $table->string('payment_method')->default('cash');
            $table->date('date');
            $table->string('attachment')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('name'); // Main Cash, City Bank, bKash Merchant
            $table->string('type')->default('cash'); // cash, bank, mobile_banking
            $table->string('account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->decimal('balance', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('account_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('account_id')->constrained('accounts')->onDelete('cascade');
            $table->string('type'); // deposit, withdraw, transfer
            $table->decimal('amount', 12, 2);
            $table->nullableMorphs('reference');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('sale_id')->nullable()->constrained('sales')->onDelete('cascade');
            $table->string('type')->default('earned'); // earned, redeemed
            $table->integer('points');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_transactions');
        Schema::dropIfExists('account_transactions');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('employees');
    }
};
