<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique();

            $table->string('category')->nullable();
            $table->string('author_name')->nullable();

            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();

            $table->string('featured_image')->nullable();

            $table->enum('status', ['draft', 'active', 'inactive'])->default('draft');
            $table->timestamp('published_at')->nullable();

            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedBigInteger('views')->default(0);

            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_keywords')->nullable();

            $table->timestamps();

            $table->index(['status', 'published_at']);
            $table->index('category');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};