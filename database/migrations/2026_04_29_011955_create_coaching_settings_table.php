<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coaching_settings', function (Blueprint $table) {
            $table->id();

            // Future multi-coaching support ke liye nullable rakha hai
            $table->unsignedBigInteger('coaching_id')->nullable()->index();

            $table->string('institute_name')->default('Edu Institute');
            $table->string('tagline')->nullable();

            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();

            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();

            $table->text('address')->nullable();
            $table->text('google_map_url')->nullable();

            $table->text('footer_description')->nullable();

            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('linkedin_url')->nullable();

            $table->string('default_seo_title')->nullable();
            $table->text('default_seo_description')->nullable();
            $table->text('default_seo_keywords')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coaching_settings');
    }
};