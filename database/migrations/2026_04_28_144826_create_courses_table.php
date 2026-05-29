<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique();
            $table->string('course_type')->nullable(); // IIT JEE, NEET, SSC, Tuition etc.
            $table->string('class_level')->nullable(); // Class 9, 10, 11, 12 etc.
            $table->string('duration')->nullable();

            $table->decimal('fee', 10, 2)->nullable();
            $table->decimal('offer_fee', 10, 2)->nullable();

            $table->string('image')->nullable();

            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->longText('subjects')->nullable();
            $table->longText('eligibility')->nullable();
            $table->longText('features')->nullable();

            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_keywords')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};