<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pwd_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('full_name');
            $table->string('oku_number')->unique();
            $table->string('oku_card_path')->nullable();
            $table->string('certificate_path')->nullable();
            $table->string('video_path')->nullable();
            $table->json('skills')->nullable();
            $table->json('accessibility_needs')->nullable();
            $table->string('digital_portfolio_link')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pwd_profiles');
    }
};