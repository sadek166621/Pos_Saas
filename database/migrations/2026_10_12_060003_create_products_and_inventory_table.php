<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('image')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->index(['tenant_id', 'slug']);
        });

        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->string('image')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->index(['tenant_id', 'slug']);
        });

        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('name'); // Piece, Kilogram, Liter, Box
            $table->string('code'); // pcs, kg, ltr, box
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->foreignId('brand_id')->nullable()->constrained('brands')->onDelete('set null');
            $table->foreignId('unit_id')->nullable()->constrained('units')->onDelete('set null');
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('sku')->nullable()->index();
            $table->string('barcode')->nullable()->index();
            $table->string('type')->default('simple'); // simple, variable, service, bundle
            $table->decimal('purchase_price', 12, 2)->default(0);
            $table->decimal('selling_price', 12, 2)->default(0);
            $table->decimal('wholesale_price', 12, 2)->nullable();
            $table->decimal('dealer_price', 12, 2)->nullable();
            $table->integer('min_stock_alert')->default(5);
            $table->decimal('vat_percent', 5, 2)->default(0);
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'name']);
        });

        Schema::create('branch_product_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->decimal('selling_price', 12, 2);
            $table->decimal('wholesale_price', 12, 2)->nullable();
            $table->decimal('dealer_price', 12, 2)->nullable();
            $table->timestamps();

            $table->unique(['branch_id', 'product_id']);
        });

        Schema::create('branch_product_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->decimal('quantity', 12, 2)->default(0);
            $table->decimal('alert_quantity', 12, 2)->default(5);
            $table->timestamps();

            $table->index(['branch_id', 'product_id']);
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('type'); // opening, sale, purchase, transfer_in, transfer_out, return, adjustment, damage
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->nullableMorphs('reference');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('from_branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('to_branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('transfer_no')->unique();
            $table->string('status')->default('pending'); // pending, in_transit, received, cancelled
            $table->unsignedBigInteger('transferred_by')->nullable();
            $table->unsignedBigInteger('received_by')->nullable();
            $table->date('transfer_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transfer_id')->constrained('stock_transfers')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->decimal('quantity_transferred', 12, 2);
            $table->decimal('quantity_received', 12, 2)->default(0);
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('adjustment_no')->unique();
            $table->string('type')->default('subtraction'); // addition, subtraction, damage
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_adjustment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('adjustment_id')->constrained('stock_adjustments')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustment_items');
        Schema::dropIfExists('stock_adjustments');
        Schema::dropIfExists('stock_transfer_items');
        Schema::dropIfExists('stock_transfers');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('branch_product_stocks');
        Schema::dropIfExists('branch_product_prices');
        Schema::dropIfExists('products');
        Schema::dropIfExists('units');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('categories');
    }
};
