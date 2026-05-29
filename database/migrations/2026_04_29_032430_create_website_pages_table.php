<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_pages', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique();

            $table->string('page_type')->default('default');

            $table->string('hero_title')->nullable();
            $table->text('hero_subtitle')->nullable();

            $table->longText('content')->nullable();

            $table->string('cta_title')->nullable();
            $table->text('cta_description')->nullable();

            $table->boolean('show_enquiry_form')->default(false);

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
        Schema::dropIfExists('website_pages');
    }
};