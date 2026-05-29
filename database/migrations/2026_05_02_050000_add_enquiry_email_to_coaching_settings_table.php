<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coaching_settings', function (Blueprint $table) {
            $table->string('enquiry_email')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('coaching_settings', function (Blueprint $table) {
            $table->dropColumn('enquiry_email');
        });
    }
};
