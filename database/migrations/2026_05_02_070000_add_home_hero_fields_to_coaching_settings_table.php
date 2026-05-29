<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coaching_settings', function (Blueprint $table) {
            $table->string('home_hero_title')->nullable()->after('font_family');
            $table->string('home_hero_highlight')->nullable()->after('home_hero_title');
            $table->text('home_hero_subtitle')->nullable()->after('home_hero_highlight');
            $table->string('home_hero_image')->nullable()->after('home_hero_subtitle');
        });
    }

    public function down(): void
    {
        Schema::table('coaching_settings', function (Blueprint $table) {
            $table->dropColumn([
                'home_hero_title',
                'home_hero_highlight',
                'home_hero_subtitle',
                'home_hero_image',
            ]);
        });
    }
};
