<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('admission_id')->nullable()->index();
            $table->unsignedBigInteger('course_id')->nullable()->index();

            $table->string('student_code')->unique();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->date('dob')->nullable();
            $table->string('gender')->nullable();

            $table->string('class_level')->nullable();
            $table->string('course_name')->nullable();

            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();

            $table->string('photo')->nullable();

            $table->enum('status', ['active', 'inactive', 'passed_out', 'left'])->default('active');

            $table->date('joining_date')->nullable();

            $table->timestamps();

            $table->index(['status', 'joining_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};