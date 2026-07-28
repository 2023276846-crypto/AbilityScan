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
            $table->json('certificates')->nullable()->after('certificate_path');
            $table->json('videos')->nullable()->after('video_path');
        });

        // Migrate existing single certificate/video paths to the new arrays
        $profiles = Illuminate\Support\Facades\DB::table('pwd_profiles')->get();
        foreach ($profiles as $profile) {
            $certs = [];
            if ($profile->certificate_path) {
                $certs[] = [
                    'name' => basename($profile->certificate_path),
                    'path' => $profile->certificate_path
                ];
            }
            $vids = [];
            if ($profile->video_path) {
                $vids[] = [
                    'name' => basename($profile->video_path),
                    'path' => $profile->video_path
                ];
            }
            Illuminate\Support\Facades\DB::table('pwd_profiles')
                ->where('id', $profile->id)
                ->update([
                    'certificates' => json_encode($certs),
                    'videos' => json_encode($vids)
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pwd_profiles', function (Blueprint $table) {
            $table->dropColumn(['certificates', 'videos']);
        });
    }
};
