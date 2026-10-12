<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action'); // created, updated, deleted, login, override, status_change
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('approval_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('type'); // large_discount, refund, purchase, stock_adjustment, price_override
            $table->nullableMorphs('reference');
            $table->unsignedBigInteger('requested_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('reason')->nullable();
            $table->timestamps();
        });

        Schema::create('sms_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade');
            $table->string('type'); // sale_invoice, due_reminder, subscription_reminder, welcome
            $table->text('template_en');
            $table->text('template_bn');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade');
            $table->string('phone');
            $table->text('message');
            $table->string('gateway')->default('mim_sms'); // mim_sms, ssl_wireless, greenweb
            $table->string('status')->default('sent'); // sent, failed
            $table->text('response')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
        Schema::dropIfExists('sms_templates');
        Schema::dropIfExists('approval_requests');
        Schema::dropIfExists('audit_logs');
    }
};
