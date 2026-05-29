<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('lead_id')->nullable()->index();
            $table->unsignedBigInteger('course_id')->nullable()->index();

            $table->string('admission_no')->unique();
            $table->date('admission_date')->nullable();

            $table->string('student_name');
            $table->string('student_phone')->nullable();
            $table->string('student_email')->nullable();
            $table->date('dob')->nullable();
            $table->string('gender')->nullable();

            $table->string('class_level')->nullable();
            $table->string('course_name')->nullable();

            $table->string('parent_name')->nullable();
            $table->string('parent_relation')->nullable();
            $table->string('parent_phone')->nullable();
            $table->string('parent_email')->nullable();

            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();

            $table->string('previous_school')->nullable();
            $table->string('source')->nullable();

            $table->decimal('registration_fee', 10, 2)->default(0);
            $table->decimal('admission_fee', 10, 2)->default(0);

            $table->enum('status', [
                'new',
                'counselling',
                'document_pending',
                'admitted',
                'rejected',
                'cancelled'
            ])->default('new');

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['status', 'admission_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admissions');
    }
};