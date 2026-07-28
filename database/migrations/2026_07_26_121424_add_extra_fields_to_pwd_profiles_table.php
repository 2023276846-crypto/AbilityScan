<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pwd_profiles', function (Blueprint $table) {
            $table->string('avatar_path')->nullable()->after('user_id');
            $table->string('location')->nullable()->after('full_name');
            $table->text('about_me')->nullable()->after('location');
            $table->text('experience')->nullable()->after('about_me');
            $table->json('education')->nullable()->after('experience');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pwd_profiles', function (Blueprint $table) {
            $table->dropColumn(['avatar_path', 'location', 'about_me', 'experience', 'education']);
        });
    }
};
