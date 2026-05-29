<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('questions') && !Schema::hasColumn('questions', 'teacher_id')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->unsignedBigInteger('teacher_id')->nullable()->after('subject_id')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('questions') && Schema::hasColumn('questions', 'teacher_id')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->dropColumn('teacher_id');
            });
        }
    }
};
