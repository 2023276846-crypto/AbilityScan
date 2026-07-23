<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pwd_id')->constrained('pwd_profiles')->onDelete('cascade');
            $table->foreignId('job_id')->constrained('job_vacancies')->onDelete('cascade');
            $table->integer('match_score')->default(0);
            $table->json('match_explanation')->nullable();
            $table->enum('application_status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_matches');
    }
};