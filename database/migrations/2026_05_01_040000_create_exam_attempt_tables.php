<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('exam_attempts')) {
            Schema::create('exam_attempts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('exam_id')->index();
                $table->unsignedBigInteger('student_id')->index();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('submitted_at')->nullable();
                $table->decimal('marks_obtained', 8, 2)->default(0);
                $table->decimal('total_marks', 8, 2)->default(0);
                $table->decimal('percentage', 5, 2)->default(0);
                $table->string('status')->default('submitted')->index();
                $table->timestamps();

                $table->index(['exam_id', 'student_id']);
            });
        }

        if (!Schema::hasTable('exam_attempt_answers')) {
            Schema::create('exam_attempt_answers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('exam_attempt_id')->index();
                $table->unsignedBigInteger('question_id')->index();
                $table->text('answer')->nullable();
                $table->decimal('marks', 8, 2)->default(0);
                $table->boolean('is_correct')->default(false);
                $table->timestamps();

                $table->unique(['exam_attempt_id', 'question_id'], 'attempt_question_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_attempt_answers');
        Schema::dropIfExists('exam_attempts');
    }
};
