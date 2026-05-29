<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('teachers')) {
            Schema::create('teachers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('qualification')->nullable();
                $table->string('experience')->nullable();
                $table->string('specialization')->nullable();
                $table->date('joining_date')->nullable();
                $table->text('address')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();

                $table->index('status');
            });
        }

        if (!Schema::hasTable('subjects')) {
            Schema::create('subjects', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->nullable()->unique();
                $table->string('class_level')->nullable();
                $table->text('description')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();

                $table->index('status');
            });
        }

        if (!Schema::hasTable('batches')) {
            Schema::create('batches', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('course_id')->nullable()->index();
                $table->unsignedBigInteger('teacher_id')->nullable()->index();
                $table->unsignedBigInteger('subject_id')->nullable()->index();

                $table->string('name');
                $table->string('code')->nullable()->unique();
                $table->string('class_level')->nullable();

                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();

                $table->string('days')->nullable();
                $table->string('room_no')->nullable();
                $table->unsignedInteger('capacity')->default(0);

                $table->enum('status', ['active', 'inactive', 'completed'])->default('active');

                $table->timestamps();

                $table->index('status');
            });
        }

        if (!Schema::hasTable('student_attendances')) {
            Schema::create('student_attendances', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('student_id')->index();
                $table->unsignedBigInteger('batch_id')->nullable()->index();

                $table->date('attendance_date');
                $table->enum('status', ['present', 'absent', 'late', 'leave'])->default('present');
                $table->text('note')->nullable();

                $table->timestamps();

                $table->unique(['student_id', 'batch_id', 'attendance_date'], 'student_attendance_unique');
                $table->index(['attendance_date', 'status']);
            });
        }

        if (!Schema::hasTable('online_classes')) {
            Schema::create('online_classes', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('batch_id')->nullable()->index();
                $table->unsignedBigInteger('teacher_id')->nullable()->index();
                $table->unsignedBigInteger('subject_id')->nullable()->index();

                $table->string('title');
                $table->date('class_date')->nullable();
                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();

                $table->string('platform')->nullable();
                $table->text('meeting_link')->nullable();
                $table->text('description')->nullable();

                $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');

                $table->timestamps();

                $table->index(['class_date', 'status']);
            });
        }

        if (!Schema::hasTable('study_materials')) {
            Schema::create('study_materials', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('batch_id')->nullable()->index();
                $table->unsignedBigInteger('teacher_id')->nullable()->index();
                $table->unsignedBigInteger('subject_id')->nullable()->index();

                $table->string('title');
                $table->string('type')->nullable();
                $table->string('file_path')->nullable();
                $table->text('external_link')->nullable();
                $table->text('description')->nullable();

                $table->enum('status', ['active', 'inactive'])->default('active');

                $table->timestamps();

                $table->index('status');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('study_materials');
        Schema::dropIfExists('online_classes');
        Schema::dropIfExists('student_attendances');
        Schema::dropIfExists('batches');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('teachers');
    }
};