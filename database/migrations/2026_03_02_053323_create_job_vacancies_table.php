<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_vacancies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained('employer_profiles')->onDelete('cascade');
            $table->string('job_title');
            $table->string('location');
            $table->text('description')->nullable();
            $table->json('requirements')->nullable();
            $table->json('office_accessibility')->nullable();
            $table->enum('job_status', ['open', 'closed'])->default('open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_vacancies');
    }
};