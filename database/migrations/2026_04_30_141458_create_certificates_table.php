<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('certificates')) {
            Schema::create('certificates', function (Blueprint $table) {
                $table->id();

                $table->string('certificate_no')->unique();

                $table->unsignedBigInteger('student_id')->nullable()->index();

                $table->string('recipient_name');
                $table->string('student_code')->nullable();

                $table->string('certificate_title')->default('Certificate of Completion');
                $table->string('certificate_type')->default('completion');

                $table->string('course_name')->nullable();
                $table->string('class_level')->nullable();
                $table->string('batch_name')->nullable();

                $table->date('issue_date')->nullable();
                $table->date('completion_date')->nullable();

                $table->string('grade')->nullable();
                $table->string('duration')->nullable();

                $table->text('description')->nullable();
                $table->text('remarks')->nullable();

                $table->string('template')->default('premium');

                $table->string('signed_by')->nullable();
                $table->string('signature_title')->nullable();

                $table->enum('status', ['active', 'cancelled'])->default('active');

                $table->timestamps();

                $table->index(['status', 'issue_date']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};