<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();

            $table->enum('type', ['image', 'youtube'])->default('image');

            $table->string('title');
            $table->string('category')->nullable();

            $table->string('image')->nullable();
            $table->string('youtube_url')->nullable();

            $table->text('description')->nullable();

            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);

            $table->timestamps();

            $table->index(['status', 'type']);
            $table->index('category');
            
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_items');
    }
};