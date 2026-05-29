<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coaching_settings', function (Blueprint $table) {
            $table->string('primary_color', 20)->default('#2563eb')->after('footer_description');
            $table->string('secondary_color', 20)->default('#7c3aed')->after('primary_color');
            $table->string('accent_color', 20)->default('#16a34a')->after('secondary_color');
            $table->string('font_family')->default('Arial')->after('accent_color');
        });
    }

    public function down(): void
    {
        Schema::table('coaching_settings', function (Blueprint $table) {
            $table->dropColumn([
                'primary_color',
                'secondary_color',
                'accent_color',
                'font_family',
            ]);
        });
    }
};
