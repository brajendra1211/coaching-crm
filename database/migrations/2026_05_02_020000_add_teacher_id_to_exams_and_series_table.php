<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('exams') && !Schema::hasColumn('exams', 'teacher_id')) {
            Schema::table('exams', function (Blueprint $table) {
                $table->unsignedBigInteger('teacher_id')->nullable()->after('batch_id')->index();
            });
        }

        if (Schema::hasTable('exam_series') && !Schema::hasColumn('exam_series', 'teacher_id')) {
            Schema::table('exam_series', function (Blueprint $table) {
                $table->unsignedBigInteger('teacher_id')->nullable()->after('batch_id')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('exams') && Schema::hasColumn('exams', 'teacher_id')) {
            Schema::table('exams', function (Blueprint $table) {
                $table->dropColumn('teacher_id');
            });
        }

        if (Schema::hasTable('exam_series') && Schema::hasColumn('exam_series', 'teacher_id')) {
            Schema::table('exam_series', function (Blueprint $table) {
                $table->dropColumn('teacher_id');
            });
        }
    }
};
