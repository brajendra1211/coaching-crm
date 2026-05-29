<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'student_id')) {
                $table->unsignedBigInteger('student_id')->nullable()->after('status')->index();
            }

            if (!Schema::hasColumn('users', 'teacher_id')) {
                $table->unsignedBigInteger('teacher_id')->nullable()->after('student_id')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'student_id')) {
                $table->dropColumn('student_id');
            }

            if (Schema::hasColumn('users', 'teacher_id')) {
                $table->dropColumn('teacher_id');
            }
        });
    }
};
