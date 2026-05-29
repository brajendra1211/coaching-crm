<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('seo_metas')) {
            Schema::create('seo_metas', function (Blueprint $table) {
                $table->id();

                $table->string('page_name')->nullable();
                $table->string('path')->unique();

                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->text('meta_keywords')->nullable();

                $table->string('canonical_url')->nullable();

                $table->string('og_title')->nullable();
                $table->text('og_description')->nullable();
                $table->string('og_image')->nullable();

                $table->string('twitter_title')->nullable();
                $table->text('twitter_description')->nullable();
                $table->string('twitter_image')->nullable();

                $table->string('robots')->default('index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1');

                $table->longText('schema_json')->nullable();

                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->unsignedInteger('sort_order')->default(0);

                $table->timestamps();

                $table->index('status');
                $table->index('sort_order');
            });

            return;
        }

        Schema::table('seo_metas', function (Blueprint $table) {
            if (!Schema::hasColumn('seo_metas', 'page_name')) {
                $table->string('page_name')->nullable()->after('id');
            }

            if (!Schema::hasColumn('seo_metas', 'path')) {
                $table->string('path')->nullable()->unique()->after('page_name');
            }

            if (!Schema::hasColumn('seo_metas', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('path');
            }

            if (!Schema::hasColumn('seo_metas', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }

            if (!Schema::hasColumn('seo_metas', 'meta_keywords')) {
                $table->text('meta_keywords')->nullable()->after('meta_description');
            }

            if (!Schema::hasColumn('seo_metas', 'canonical_url')) {
                $table->string('canonical_url')->nullable()->after('meta_keywords');
            }

            if (!Schema::hasColumn('seo_metas', 'og_title')) {
                $table->string('og_title')->nullable()->after('canonical_url');
            }

            if (!Schema::hasColumn('seo_metas', 'og_description')) {
                $table->text('og_description')->nullable()->after('og_title');
            }

            if (!Schema::hasColumn('seo_metas', 'og_image')) {
                $table->string('og_image')->nullable()->after('og_description');
            }

            if (!Schema::hasColumn('seo_metas', 'twitter_title')) {
                $table->string('twitter_title')->nullable()->after('og_image');
            }

            if (!Schema::hasColumn('seo_metas', 'twitter_description')) {
                $table->text('twitter_description')->nullable()->after('twitter_title');
            }

            if (!Schema::hasColumn('seo_metas', 'twitter_image')) {
                $table->string('twitter_image')->nullable()->after('twitter_description');
            }

            if (!Schema::hasColumn('seo_metas', 'robots')) {
                $table->string('robots')->default('index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1')->after('twitter_image');
            }

            if (!Schema::hasColumn('seo_metas', 'schema_json')) {
                $table->longText('schema_json')->nullable()->after('robots');
            }

            if (!Schema::hasColumn('seo_metas', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('schema_json');
            }

            if (!Schema::hasColumn('seo_metas', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_metas');
    }
};