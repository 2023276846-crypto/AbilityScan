<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('admin_id')->nullable()->unique()->after('role');
        });

        // Generate Admin IDs for existing admin users
        $admins = \DB::table('users')->where('role', 'admin')->orderBy('id', 'asc')->get();
        $year = date('Y');
        $seq = 1;
        foreach ($admins as $admin) {
            $adminId = sprintf("ADM-%s-%03d", $year, $seq++);
            \DB::table('users')->where('id', $admin->id)->update(['admin_id' => $adminId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('admin_id');
        });
    }
};
