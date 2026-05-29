<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coaching_settings', function (Blueprint $table) {
            $table->string('home_seo_title')->nullable()->after('home_hero_image');
            $table->text('home_seo_description')->nullable()->after('home_seo_title');
            $table->text('home_seo_keywords')->nullable()->after('home_seo_description');
            $table->string('students_count')->nullable()->after('home_seo_keywords');
            $table->string('courses_count')->nullable()->after('students_count');
            $table->string('tests_count')->nullable()->after('courses_count');
            $table->string('rating')->nullable()->after('tests_count');
        });
    }

    public function down(): void
    {
        Schema::table('coaching_settings', function (Blueprint $table) {
            $table->dropColumn([
                'home_seo_title',
                'home_seo_description',
                'home_seo_keywords',
                'students_count',
                'courses_count',
                'tests_count',
                'rating',
            ]);
        });
    }
};
