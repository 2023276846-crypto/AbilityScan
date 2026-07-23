<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PwdProfile;
use App\Models\EmployerProfile;
use Illuminate\Support\Facades\Hash;

class MalayPwdUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. FIRST 30 PWD USERS (pwd3 to pwd32)
        $malayNamesPart1 = [
            'Muhammad Faiz bin Rahim',
            'Siti Aminah binti Abdullah',
            'Ahmad Ridzuan bin Osman',
            'Nurul Farhana binti Rosli',
            'Mohd Syamil bin Zulkifli',
            'Norhafizah binti Mohd Yusof',
            'Nor Azmi bin Hashim',
            'Farah Nabila binti Azman',
            'Khairul Anuar bin Ibrahim',
            'Aishah binti Abu Bakar',
            'Abdul Halim bin Sajad',
            'Nurul Hidayah binti Kamaruzaman',
            'Muhammad Hakimi bin Rosli',
            'Siti Sarah binti Mazlan',
            'Shahrul Azhar bin Ramli',
            'Anis Farhana binti Zulkifli',
            'Mohd Fitri bin Kamaruddin',
            'Nor Diana binti Kamaruddin',
            'Amirul Ashraf bin Yazid',
            'Nur Syazwani binti Mohd Noor',
            'Muhammad Hafiz bin Roslan',
            'Fatin Shahirah binti Salleh',
            'Azhar bin Ismail',
            'Nurul Asyikin binti Hamzah',
            'Zulkifli bin Ahmad',
            'Siti Nurhaliza binti Tarudin',
            'Wan Mohd Hazim bin Wan Ibrahim',
            'Amira Sofia binti Shamsul',
            'Muhammad Aiman bin Harun',
            'Nur Sabrina binti Ghazali',
        ];

        $startIndexPart1 = 3;
        foreach ($malayNamesPart1 as $index => $name) {
            $emailIndex = $startIndexPart1 + $index;
            $email = "pwd{$emailIndex}@gmail.com";

            $user = User::where('email', $email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make('pass123'),
                    'role' => 'pwd',
                ]);

                PwdProfile::factory()->create([
                    'user_id' => $user->id,
                    'full_name' => $name,
                    'oku_number' => 'OKU' . sprintf('%08d', rand(10000000, 99999999)),
                ]);
            } else {
                $user->update([
                    'name' => $name,
                    'password' => Hash::make('pass123'),
                ]);

                $profile = PwdProfile::where('user_id', $user->id)->first();
                if ($profile) {
                    $profile->update([
                        'full_name' => $name,
                    ]);
                } else {
                    PwdProfile::factory()->create([
                        'user_id' => $user->id,
                        'full_name' => $name,
                        'oku_number' => 'OKU' . sprintf('%08d', rand(10000000, 99999999)),
                    ]);
                }
            }
        }

        // 2. NEXT 30 PWD USERS (pwd33 to pwd62)
        $malayNamesPart2 = [
            'Mohd Azlan bin Mansor',
            'Muhammad Danial bin Roslan',
            'Khairul Nizam bin Salleh',
            'Syahrul Nizam bin Ahmad',
            'Muhammad Luqman bin Hakim',
            'Mohd Ridzuan bin Sidek',
            'Muhammad Firdaus bin Kamaruddin',
            'Ahmad Syakir bin Mohd Noor',
            'Nor Helmi bin Azmi',
            'Muhammad Zarif bin Ismail',
            'Mohd Haziq bin Zulkifli',
            'Wan Ahmad Fauzi bin Wan Yusoff',
            'Muhammad Alif bin Haron',
            'Muhammad Syahmi bin Razak',
            'Mohd Najib bin Ismail',
            'Nurul Izzah binti Mohd Yusof',
            'Siti Zubaidah binti Rahman',
            'Nor Syamimi binti Abdul Halim',
            'Farah Shahirah binti Azhar',
            'Aina Sofia binti Zulkifli',
            'Nurul Husna binti Kamaruzzaman',
            'Siti Fatimah binti Ibrahim',
            'Anis Nabila binti Roslan',
            'Nor Hidayah binti Salleh',
            'Nur Azrina binti Ghazali',
            'Fatin Nuraisyah binti Hamzah',
            'Nurul Syahirah binti Kamaruddin',
            'Siti Khadijah binti Wan Ahmad',
            'Amira Syuhada binti Shamsul',
            'Nur Athirah binti Ghazali',
        ];

        $startIndexPart2 = 33;
        foreach ($malayNamesPart2 as $index => $name) {
            $emailIndex = $startIndexPart2 + $index;
            $email = "pwd{$emailIndex}@gmail.com";

            $user = User::where('email', $email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make('pass123'),
                    'role' => 'pwd',
                ]);

                PwdProfile::factory()->create([
                    'user_id' => $user->id,
                    'full_name' => $name,
                    'oku_number' => 'OKU' . sprintf('%08d', rand(10000000, 99999999)),
                ]);
            } else {
                $user->update([
                    'name' => $name,
                    'password' => Hash::make('pass123'),
                ]);

                $profile = PwdProfile::where('user_id', $user->id)->first();
                if ($profile) {
                    $profile->update([
                        'full_name' => $name,
                    ]);
                } else {
                    PwdProfile::factory()->create([
                        'user_id' => $user->id,
                        'full_name' => $name,
                        'oku_number' => 'OKU' . sprintf('%08d', rand(10000000, 99999999)),
                    ]);
                }
            }
        }

        // 3. 30 EMPLOYER USERS (employer1 to employer30)
        $companies = [
            ['name' => 'Maybank Berhad', 'industry' => 'Finance'],
            ['name' => 'Tenaga Nasional Berhad', 'industry' => 'Energy'],
            ['name' => 'Petronas Dagangan', 'industry' => 'Energy'],
            ['name' => 'Maxis Communications', 'industry' => 'Technology'],
            ['name' => 'CelcomDigi Berhad', 'industry' => 'Technology'],
            ['name' => 'CIMB Group Holdings', 'industry' => 'Finance'],
            ['name' => 'Telekom Malaysia', 'industry' => 'Technology'],
            ['name' => 'Sime Darby Berhad', 'industry' => 'Agriculture'],
            ['name' => 'Top Glove Corporation', 'industry' => 'Manufacturing'],
            ['name' => 'Hartalega Holdings', 'industry' => 'Manufacturing'],
            ['name' => 'Astro Malaysia Holdings', 'industry' => 'Media'],
            ['name' => 'AirAsia Group', 'industry' => 'Transportation'],
            ['name' => 'Grab Malaysia', 'industry' => 'Technology'],
            ['name' => 'Shopee Malaysia', 'industry' => 'Retail'],
            ['name' => 'Mydin Mohamed Holdings', 'industry' => 'Retail'],
            ['name' => 'Public Bank Berhad', 'industry' => 'Finance'],
            ['name' => 'RHB Bank Berhad', 'industry' => 'Finance'],
            ['name' => 'Axiata Group', 'industry' => 'Technology'],
            ['name' => 'Gamuda Berhad', 'industry' => 'Construction'],
            ['name' => 'IJM Corporation', 'industry' => 'Construction'],
            ['name' => 'Sunway Group', 'industry' => 'Conglomerate'],
            ['name' => 'YTL Corporation', 'industry' => 'Conglomerate'],
            ['name' => 'IOI Corporation', 'industry' => 'Agriculture'],
            ['name' => 'Nestlé Malaysia', 'industry' => 'Healthcare'],
            ['name' => 'Fraser & Neave Holdings', 'industry' => 'Retail'],
            ['name' => 'KPJ Healthcare Berhad', 'industry' => 'Healthcare'],
            ['name' => 'UEM Sunrise', 'industry' => 'Construction'],
            ['name' => 'Padini Holdings', 'industry' => 'Retail'],
            ['name' => 'POS Malaysia', 'industry' => 'Transportation'],
            ['name' => 'MR D.I.Y. Group', 'industry' => 'Retail'],
        ];

        foreach ($companies as $index => $company) {
            $emailIndex = $index + 1;
            $email = "employer{$emailIndex}@gmail.com";

            $user = User::where('email', $email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $company['name'],
                    'email' => $email,
                    'password' => Hash::make('pass123'),
                    'role' => 'employer',
                ]);

                EmployerProfile::factory()->create([
                    'user_id' => $user->id,
                    'company_name' => $company['name'],
                    'industry_type' => $company['industry'],
                    'company_email' => $email,
                    'ssm_number' => sprintf('%08d', rand(100000, 999999)) . '-X',
                ]);
            } else {
                $user->update([
                    'name' => $company['name'],
                    'password' => Hash::make('pass123'),
                ]);

                $profile = EmployerProfile::where('user_id', $user->id)->first();
                if ($profile) {
                    $profile->update([
                        'company_name' => $company['name'],
                        'industry_type' => $company['industry'],
                        'company_email' => $email,
                    ]);
                } else {
                    EmployerProfile::factory()->create([
                        'user_id' => $user->id,
                        'company_name' => $company['name'],
                        'industry_type' => $company['industry'],
                        'company_email' => $email,
                        'ssm_number' => sprintf('%08d', rand(100000, 999999)) . '-X',
                    ]);
                }
            }
        }

        $this->command->info('Successfully generated 60 PWD users (pwd3 to pwd62) and 30 Employer users (employer1 to employer30)!');
    }
}
