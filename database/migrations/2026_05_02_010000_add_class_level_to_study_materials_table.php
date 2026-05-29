<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('study_materials') && !Schema::hasColumn('study_materials', 'class_level')) {
            Schema::table('study_materials', function (Blueprint $table) {
                $table->string('class_level')->nullable()->after('subject_id')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('study_materials') && Schema::hasColumn('study_materials', 'class_level')) {
            Schema::table('study_materials', function (Blueprint $table) {
                $table->dropColumn('class_level');
            });
        }
    }
};
