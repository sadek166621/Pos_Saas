<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('opening_cash', 12, 2)->default(0);
            $table->decimal('closing_cash', 12, 2)->nullable();
            $table->decimal('expected_cash', 12, 2)->nullable();
            $table->decimal('cash_sales', 12, 2)->default(0);
            $table->decimal('cash_in', 12, 2)->default(0);
            $table->decimal('cash_out', 12, 2)->default(0);
            $table->decimal('expense_total', 12, 2)->default(0);
            $table->string('status')->default('open'); // open, closed
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->foreignId('shift_id')->nullable()->constrained('cash_shifts')->onDelete('set null');
            $table->string('invoice_no')->unique();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->string('discount_type')->default('fixed'); // fixed, percentage
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('vat_amount', 12, 2)->default(0);
            $table->decimal('service_charge', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('paid', 12, 2)->default(0);
            $table->decimal('due', 12, 2)->default(0);
            $table->string('payment_status')->default('paid'); // paid, partial, due
            $table->string('status')->default('completed'); // completed, draft, held, cancelled, returned
            $table->string('payment_method')->default('cash'); // cash, bkash, nagad, rocket, card, bank, split, due
            $table->unsignedBigInteger('sold_by')->nullable();
            $table->dateTime('sale_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'branch_id', 'sale_date']);
        });

        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('product_name');
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('cost_price', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('vat', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });

        Schema::create('sale_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('sale_id')->nullable()->constrained('sales')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade');
            $table->string('payment_no')->unique();
            $table->string('method')->default('cash'); // cash, bkash, nagad, rocket, card, bank
            $table->decimal('amount', 12, 2);
            $table->string('trx_id')->nullable();
            $table->timestamp('paid_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('sale_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->string('return_no')->unique();
            $table->decimal('refund_amount', 12, 2);
            $table->string('refund_type')->default('cash'); // cash, store_credit, due_deduct
            $table->string('status')->default('approved'); // approved, pending, rejected
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();
        });

        Schema::create('sale_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_return_id')->constrained('sale_returns')->onDelete('cascade');
            $table->foreignId('sale_item_id')->nullable()->constrained('sale_items')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->decimal('quantity', 12, 2);
            $table->decimal('refund_price', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_return_items');
        Schema::dropIfExists('sale_returns');
        Schema::dropIfExists('sale_payments');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('cash_shifts');
    }
};
