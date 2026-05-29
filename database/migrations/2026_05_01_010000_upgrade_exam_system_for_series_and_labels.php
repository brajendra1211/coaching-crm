<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('questions')) {
            Schema::table('questions', function (Blueprint $table) {
                if (!Schema::hasColumn('questions', 'label')) {
                    $table->string('label')->nullable()->after('class_level')->index();
                }

                if (!Schema::hasColumn('questions', 'topic')) {
                    $table->string('topic')->nullable()->after('label')->index();
                }

                if (!Schema::hasColumn('questions', 'source')) {
                    $table->string('source')->nullable()->after('topic');
                }

                if (!Schema::hasColumn('questions', 'tags')) {
                    $table->text('tags')->nullable()->after('source');
                }
            });
        }

        if (Schema::hasTable('exams')) {
            Schema::table('exams', function (Blueprint $table) {
                if (!Schema::hasColumn('exams', 'exam_type')) {
                    $table->string('exam_type')->default('single')->after('exam_code')->index();
                }

                if (!Schema::hasColumn('exams', 'label')) {
                    $table->string('label')->nullable()->after('exam_type')->index();
                }

                if (!Schema::hasColumn('exams', 'difficulty')) {
                    $table->string('difficulty')->default('medium')->after('label')->index();
                }

                if (!Schema::hasColumn('exams', 'access_type')) {
                    $table->string('access_type')->default('free')->after('difficulty')->index();
                }

                if (!Schema::hasColumn('exams', 'price')) {
                    $table->decimal('price', 10, 2)->default(0)->after('access_type');
                }

                if (!Schema::hasColumn('exams', 'negative_marks')) {
                    $table->decimal('negative_marks', 8, 2)->default(0)->after('passing_marks');
                }

                if (!Schema::hasColumn('exams', 'attempt_limit')) {
                    $table->unsignedInteger('attempt_limit')->default(1)->after('negative_marks');
                }

                if (!Schema::hasColumn('exams', 'show_result_immediately')) {
                    $table->boolean('show_result_immediately')->default(true)->after('attempt_limit');
                }
            });
        }

        if (!Schema::hasTable('exam_series')) {
            Schema::create('exam_series', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('batch_id')->nullable()->index();
                $table->string('title');
                $table->string('series_code')->nullable()->unique();
                $table->string('label')->nullable()->index();
                $table->string('difficulty')->default('medium')->index();
                $table->string('access_type')->default('free')->index();
                $table->decimal('price', 10, 2)->default(0);
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->text('description')->nullable();
                $table->text('instructions')->nullable();
                $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->index();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('exam_series_exam')) {
            Schema::create('exam_series_exam', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('exam_series_id')->index();
                $table->unsignedBigInteger('exam_id')->index();
                $table->unsignedInteger('sort_order')->default(0);
                $table->string('unlock_rule')->default('always');
                $table->timestamps();

                $table->unique(['exam_series_id', 'exam_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_series_exam');
        Schema::dropIfExists('exam_series');
    }
};
