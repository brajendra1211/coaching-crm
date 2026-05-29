<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coaching_settings', function (Blueprint $table) {
            $table->string('mail_from_name')->nullable()->after('enquiry_email');
            $table->string('mail_from_address')->nullable()->after('mail_from_name');
            $table->string('gmail_address')->nullable()->after('mail_from_address');
            $table->text('gmail_app_password')->nullable()->after('gmail_address');
        });
    }

    public function down(): void
    {
        Schema::table('coaching_settings', function (Blueprint $table) {
            $table->dropColumn([
                'mail_from_name',
                'mail_from_address',
                'gmail_address',
                'gmail_app_password',
            ]);
        });
    }
};
