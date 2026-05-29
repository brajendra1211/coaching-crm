<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('batch_students')) {
            Schema::create('batch_students', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('batch_id')->index();
                $table->unsignedBigInteger('student_id')->index();

                $table->date('assigned_at')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');

                $table->timestamps();

                $table->unique(['batch_id', 'student_id']);
            });
        }

        if (!Schema::hasTable('batch_teachers')) {
            Schema::create('batch_teachers', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('batch_id')->index();
                $table->unsignedBigInteger('teacher_id')->index();
                $table->unsignedBigInteger('subject_id')->nullable()->index();

                $table->enum('role', ['primary', 'assistant'])->default('primary');
                $table->date('assigned_at')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');

                $table->timestamps();

                $table->unique(['batch_id', 'teacher_id', 'subject_id'], 'batch_teacher_subject_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_teachers');
        Schema::dropIfExists('batch_students');
    }
};