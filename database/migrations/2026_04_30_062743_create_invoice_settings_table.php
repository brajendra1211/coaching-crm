<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_settings', function (Blueprint $table) {
            $table->id();

            $table->string('invoice_title')->default('Fee Receipt');
            $table->string('invoice_prefix')->default('RCPT');
            $table->string('default_template')->default('modern'); // modern, classic, compact

            $table->string('paper_size')->default('A4'); // A4, A5
            $table->string('accent_color')->default('#2563eb');

            $table->boolean('show_logo')->default(true);
            $table->boolean('show_address')->default(true);
            $table->boolean('show_phone')->default(true);
            $table->boolean('show_email')->default(true);
            $table->boolean('show_signature')->default(true);
            $table->boolean('show_balance')->default(true);

            $table->string('authorized_signature_label')->default('Authorized Signature');
            $table->text('terms')->nullable();
            $table->text('footer_note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_settings');
    }
};