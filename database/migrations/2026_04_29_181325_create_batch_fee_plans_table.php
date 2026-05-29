<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('batch_fee_plans')) {
            Schema::create('batch_fee_plans', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('batch_id')->index();

                $table->string('title');
                $table->string('billing_type')->default('monthly'); 
                // monthly, one_time, installment

                $table->decimal('registration_fee', 10, 2)->default(0);
                $table->decimal('admission_fee', 10, 2)->default(0);
                $table->decimal('tuition_fee', 10, 2)->default(0);
                $table->decimal('exam_fee', 10, 2)->default(0);
                $table->decimal('material_fee', 10, 2)->default(0);
                $table->decimal('other_fee', 10, 2)->default(0);

                $table->unsignedTinyInteger('due_day')->nullable();
                $table->decimal('fine_per_day', 10, 2)->default(0);

                $table->date('effective_from')->nullable();
                $table->date('effective_to')->nullable();

                $table->text('notes')->nullable();

                $table->enum('status', ['active', 'inactive'])->default('active');

                $table->timestamps();

                $table->index(['batch_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_fee_plans');
    }
};