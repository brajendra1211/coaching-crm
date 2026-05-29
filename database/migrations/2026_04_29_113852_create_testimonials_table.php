<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('testimonials')) {
            Schema::create('testimonials', function (Blueprint $table) {
                $table->id();

                $table->string('name');
                $table->string('designation')->nullable();
                $table->string('course_name')->nullable();
                $table->string('location')->nullable();

                $table->string('image')->nullable();
                $table->text('review');
                $table->unsignedTinyInteger('rating')->default(5);

                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->boolean('is_featured')->default(false);
                $table->unsignedInteger('sort_order')->default(0);

                $table->timestamps();

                $table->index(['status', 'is_featured']);
                $table->index('sort_order');
            });

            return;
        }

        Schema::table('testimonials', function (Blueprint $table) {
            if (!Schema::hasColumn('testimonials', 'name')) {
                $table->string('name')->nullable()->after('id');
            }

            if (!Schema::hasColumn('testimonials', 'designation')) {
                $table->string('designation')->nullable()->after('name');
            }

            if (!Schema::hasColumn('testimonials', 'course_name')) {
                $table->string('course_name')->nullable()->after('designation');
            }

            if (!Schema::hasColumn('testimonials', 'location')) {
                $table->string('location')->nullable()->after('course_name');
            }

            if (!Schema::hasColumn('testimonials', 'image')) {
                $table->string('image')->nullable()->after('location');
            }

            if (!Schema::hasColumn('testimonials', 'review')) {
                $table->text('review')->nullable()->after('image');
            }

            if (!Schema::hasColumn('testimonials', 'rating')) {
                $table->unsignedTinyInteger('rating')->default(5)->after('review');
            }

            if (!Schema::hasColumn('testimonials', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('rating');
            }

            if (!Schema::hasColumn('testimonials', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('status');
            }

            if (!Schema::hasColumn('testimonials', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('is_featured');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};