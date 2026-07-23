<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Update pwd_profiles
        Schema::table('pwd_profiles', function (Blueprint $table) {
            $table->json('physical_needs')->nullable()->after('skills');
            $table->json('tech_needs')->nullable()->after('physical_needs');
            $table->json('work_arrangement')->nullable()->after('tech_needs');
            $table->json('sensory_needs')->nullable()->after('work_arrangement');
        });

        // Update job_vacancies
        Schema::table('job_vacancies', function (Blueprint $table) {
            $table->json('physical_facilities')->nullable()->after('requirements');
            $table->json('tech_support')->nullable()->after('physical_facilities');
            $table->json('work_arrangement')->nullable()->after('tech_support');
            $table->json('sensory_support')->nullable()->after('work_arrangement');
        });
    }

    public function down(): void
    {
        Schema::table('pwd_profiles', function (Blueprint $table) {
            $table->dropColumn(['physical_needs', 'tech_needs', 'work_arrangement', 'sensory_needs']);
        });

        Schema::table('job_vacancies', function (Blueprint $table) {
            $table->dropColumn(['physical_facilities', 'tech_support', 'work_arrangement', 'sensory_support']);
        });
    }
};