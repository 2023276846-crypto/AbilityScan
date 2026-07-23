<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PwdProfile;
use App\Models\EmployerProfile;

class DemoUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 30 PWD Users
        User::factory()->count(30)->create(['role' => 'pwd'])->each(function ($user) {
            PwdProfile::factory()->create(['user_id' => $user->id]);
        });

        // Create 20 Employer Users
        User::factory()->count(20)->create(['role' => 'employer'])->each(function ($user) {
            EmployerProfile::factory()->create(['user_id' => $user->id]);
        });

        $this->command->info('Successfully generated 30 PWD users and 20 Employer users!');
    }
}
