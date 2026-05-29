<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('blogs')) {
            Schema::create('blogs', function (Blueprint $table) {
                $table->id();

                $table->string('title')->nullable();
                $table->string('slug')->nullable()->unique();

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

            return;
        }

        Schema::table('blogs', function (Blueprint $table) {
            if (!Schema::hasColumn('blogs', 'title')) {
                $table->string('title')->nullable()->after('id');
            }

            if (!Schema::hasColumn('blogs', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('title');
            }

            if (!Schema::hasColumn('blogs', 'category')) {
                $table->string('category')->nullable()->after('slug');
            }

            if (!Schema::hasColumn('blogs', 'author_name')) {
                $table->string('author_name')->nullable()->after('category');
            }

            if (!Schema::hasColumn('blogs', 'excerpt')) {
                $table->text('excerpt')->nullable()->after('author_name');
            }

            if (!Schema::hasColumn('blogs', 'content')) {
                $table->longText('content')->nullable()->after('excerpt');
            }

            if (!Schema::hasColumn('blogs', 'featured_image')) {
                $table->string('featured_image')->nullable()->after('content');
            }

            if (!Schema::hasColumn('blogs', 'status')) {
                $table->enum('status', ['draft', 'active', 'inactive'])->default('draft')->after('featured_image');
            }

            if (!Schema::hasColumn('blogs', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('status');
            }

            if (!Schema::hasColumn('blogs', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('published_at');
            }

            if (!Schema::hasColumn('blogs', 'views')) {
                $table->unsignedBigInteger('views')->default(0)->after('sort_order');
            }

            if (!Schema::hasColumn('blogs', 'seo_title')) {
                $table->string('seo_title')->nullable()->after('views');
            }

            if (!Schema::hasColumn('blogs', 'seo_description')) {
                $table->text('seo_description')->nullable()->after('seo_title');
            }

            if (!Schema::hasColumn('blogs', 'seo_keywords')) {
                $table->text('seo_keywords')->nullable()->after('seo_description');
            }
        });
    }

    public function down(): void
    {
        // Existing data safe rakhne ke liye table drop nahi kar rahe.
        // Agar fresh project ho aur rollback chahiye ho, tab manually drop kar sakte hain.
    }
};