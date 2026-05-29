<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();

            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('class_level')->nullable();

            $table->string('source')->nullable(); // home, course_detail, contact etc.
            $table->text('message')->nullable();

            $table->enum('status', [
                'new',
                'contacted',
                'interested',
                'not_interested',
                'converted',
                'lost'
            ])->default('new');

            $table->date('follow_up_date')->nullable();
            $table->text('admin_notes')->nullable();

            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};