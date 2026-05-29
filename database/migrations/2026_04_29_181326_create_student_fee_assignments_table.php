<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('student_fee_assignments')) {
            Schema::create('student_fee_assignments', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('student_id')->index();
                $table->unsignedBigInteger('batch_id')->index();
                $table->unsignedBigInteger('batch_fee_plan_id')->nullable()->index();

                $table->string('billing_type')->default('monthly');

                $table->decimal('registration_fee', 10, 2)->default(0);
                $table->decimal('admission_fee', 10, 2)->default(0);
                $table->decimal('tuition_fee', 10, 2)->default(0);
                $table->decimal('exam_fee', 10, 2)->default(0);
                $table->decimal('material_fee', 10, 2)->default(0);
                $table->decimal('other_fee', 10, 2)->default(0);

                $table->decimal('total_amount', 10, 2)->default(0);
                $table->decimal('paid_amount', 10, 2)->default(0);
                $table->decimal('discount_amount', 10, 2)->default(0);
                $table->decimal('fine_amount', 10, 2)->default(0);
                $table->decimal('balance_amount', 10, 2)->default(0);

                $table->unsignedTinyInteger('due_day')->nullable();
                $table->date('next_due_date')->nullable();

                $table->date('assigned_at')->nullable();

                $table->enum('status', ['active', 'inactive', 'paid', 'cancelled'])->default('active');

                $table->timestamps();

                $table->unique(['student_id', 'batch_id', 'batch_fee_plan_id'], 'student_batch_fee_unique');
                $table->index(['status', 'next_due_date']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('student_fee_assignments');
    }
};