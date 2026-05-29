<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();

            $table->string('receipt_no')->unique();

            $table->unsignedBigInteger('student_fee_assignment_id')->index();
            $table->unsignedBigInteger('student_id')->index();
            $table->unsignedBigInteger('batch_id')->index();

            $table->date('payment_date');

            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('fine_amount', 10, 2)->default(0);

            $table->decimal('total_before_payment', 10, 2)->default(0);
            $table->decimal('balance_before_payment', 10, 2)->default(0);
            $table->decimal('balance_after_payment', 10, 2)->default(0);

            $table->string('payment_mode')->default('cash');
            $table->string('transaction_id')->nullable();

            $table->text('notes')->nullable();

            $table->enum('status', ['paid', 'void'])->default('paid');

            $table->timestamps();

            $table->index(['payment_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_payments');
    }
};