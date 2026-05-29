<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_locations', function (Blueprint $table) {
            $table->id();

            $table->string('location_name');
            $table->string('focus_keyword')->nullable();

            $table->string('page_title');
            $table->string('slug')->unique();

            $table->text('short_description')->nullable();
            $table->longText('content')->nullable();

            $table->string('cta_title')->nullable();
            $table->text('cta_description')->nullable();

            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->integer('sort_order')->default(0);

            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_keywords')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_locations');
    }
};
