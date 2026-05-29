<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('questions')) {
            Schema::create('questions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('subject_id')->nullable()->index();
                $table->string('class_level')->nullable();
                $table->string('question_type')->default('mcq');
                $table->text('question');
                $table->text('option_a')->nullable();
                $table->text('option_b')->nullable();
                $table->text('option_c')->nullable();
                $table->text('option_d')->nullable();
                $table->string('correct_answer')->nullable();
                $table->decimal('marks', 8, 2)->default(1);
                $table->string('difficulty')->default('medium');
                $table->text('explanation')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();

                $table->index(['question_type', 'status']);
            });
        }

        if (!Schema::hasTable('exams')) {
            Schema::create('exams', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('batch_id')->nullable()->index();
                $table->unsignedBigInteger('subject_id')->nullable()->index();
                $table->string('title');
                $table->string('exam_code')->nullable()->unique();
                $table->date('exam_date')->nullable();
                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();
                $table->unsignedInteger('duration_minutes')->default(60);
                $table->decimal('total_marks', 8, 2)->default(0);
                $table->decimal('passing_marks', 8, 2)->default(0);
                $table->text('instructions')->nullable();
                $table->enum('status', ['draft', 'scheduled', 'completed', 'cancelled'])->default('draft');
                $table->timestamps();

                $table->index(['exam_date', 'status']);
            });
        }

        if (!Schema::hasTable('exam_question')) {
            Schema::create('exam_question', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('exam_id')->index();
                $table->unsignedBigInteger('question_id')->index();
                $table->decimal('marks', 8, 2)->default(1);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->unique(['exam_id', 'question_id']);
            });
        }

        if (!Schema::hasTable('exam_results')) {
            Schema::create('exam_results', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('exam_id')->index();
                $table->unsignedBigInteger('student_id')->index();
                $table->decimal('marks_obtained', 8, 2)->default(0);
                $table->decimal('total_marks', 8, 2)->default(0);
                $table->decimal('percentage', 5, 2)->default(0);
                $table->string('grade')->nullable();
                $table->string('result_status')->default('pass');
                $table->unsignedInteger('rank')->nullable();
                $table->text('remarks')->nullable();
                $table->date('published_at')->nullable();
                $table->timestamps();

                $table->unique(['exam_id', 'student_id']);
                $table->index(['result_status', 'rank']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_results');
        Schema::dropIfExists('exam_question');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('questions');
    }
};
