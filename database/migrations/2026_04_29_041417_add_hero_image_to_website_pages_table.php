<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('website_pages', function (Blueprint $table) {
            if (!Schema::hasColumn('website_pages', 'hero_image')) {
                $table->string('hero_image')->nullable()->after('hero_subtitle');
            }
        });
    }

    public function down(): void
    {
        Schema::table('website_pages', function (Blueprint $table) {
            if (Schema::hasColumn('website_pages', 'hero_image')) {
                $table->dropColumn('hero_image');
            }
        });
    }
};