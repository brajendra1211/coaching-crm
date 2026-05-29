<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('sa_plans')) {
            Schema::create('sa_plans', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->decimal('monthly_price', 10, 2)->default(0);
                $table->decimal('yearly_price', 10, 2)->default(0);
                $table->unsignedInteger('student_limit')->nullable();
                $table->unsignedInteger('staff_limit')->nullable();
                $table->unsignedInteger('storage_limit_mb')->nullable();
                $table->text('features')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active')->index();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('sa_tenants')) {
            Schema::create('sa_tenants', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('plan_id')->nullable()->index();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('owner_name')->nullable();
                $table->string('owner_email')->nullable();
                $table->string('owner_phone')->nullable();
                $table->string('database_name')->unique();
                $table->string('database_host')->nullable();
                $table->string('database_username')->nullable();
                $table->text('database_password')->nullable();
                $table->string('storage_path')->nullable();
                $table->enum('status', ['active', 'inactive', 'suspended', 'expired'])->default('active')->index();
                $table->date('trial_ends_at')->nullable();
                $table->date('subscription_ends_at')->nullable()->index();
                $table->timestamp('last_notified_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('sa_domains')) {
            Schema::create('sa_domains', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->index();
                $table->string('domain')->unique();
                $table->string('verification_token')->nullable();
                $table->string('verification_method')->default('dns_txt');
                $table->enum('status', ['pending', 'verified', 'failed'])->default('pending')->index();
                $table->timestamp('verified_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('sa_subscriptions')) {
            Schema::create('sa_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->index();
                $table->unsignedBigInteger('plan_id')->nullable()->index();
                $table->string('billing_cycle')->default('monthly');
                $table->decimal('amount', 10, 2)->default(0);
                $table->date('starts_at')->nullable();
                $table->date('ends_at')->nullable()->index();
                $table->enum('status', ['active', 'pending', 'expired', 'cancelled'])->default('active')->index();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('sa_payments')) {
            Schema::create('sa_payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->index();
                $table->unsignedBigInteger('subscription_id')->nullable()->index();
                $table->string('invoice_no')->nullable()->unique();
                $table->decimal('amount', 10, 2)->default(0);
                $table->date('payment_date')->nullable();
                $table->string('payment_mode')->nullable();
                $table->string('transaction_id')->nullable();
                $table->enum('status', ['paid', 'pending', 'failed'])->default('paid')->index();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sa_payments');
        Schema::dropIfExists('sa_subscriptions');
        Schema::dropIfExists('sa_domains');
        Schema::dropIfExists('sa_tenants');
        Schema::dropIfExists('sa_plans');
    }
};
