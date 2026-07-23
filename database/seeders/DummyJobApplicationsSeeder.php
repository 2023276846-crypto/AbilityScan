<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmployerProfile;
use App\Models\PwdProfile;
use App\Models\JobVacancy;
use App\Models\JobMatch;
use App\Http\Controllers\PWDController;
use Illuminate\Support\Carbon;

class DummyJobApplicationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        // 1. Get all employers
        $employers = EmployerProfile::all();
        if ($employers->isEmpty()) {
            $this->command->error('No employers found. Please run MalayPwdUsersSeeder first.');
            return;
        }

        // 2. Define pool of job titles, requirements, and facilities
        $jobTitles = [
            'Customer Service Executive',
            'Data Entry Clerk',
            'Administrative Assistant',
            'Social Media Specialist',
            'Graphic Designer',
            'Junior PHP Developer',
            'Telemarketer',
            'Accounting Assistant',
            'IT Support Technician',
            'Content Writer'
        ];

        $locations = ['Kuala Lumpur', 'Petaling Jaya', 'Penang', 'Johor Bahru', 'Shah Alam', 'Melaka', 'Ipoh'];

        $skillsPool = ['Data Entry', 'Customer Service', 'Graphic Design', 'Accounting', 'Programming', 'Communication', 'Microsoft Office', 'Copywriting', 'IT Support'];
        $physicalPool = ['Wheelchair Ramp', 'Elevator Access', 'Accessible Restroom'];
        $techPool = ['Screen Reader Software', 'Adjustable Workstation', 'Braille Keyboard'];
        $workPool = ['Flexible Working Hours', 'Remote Work', 'Hybrid Work'];
        $sensoryPool = ['Sign Language Interpreter', 'Written Communication', 'Quiet Space'];

        $pwdController = new PWDController();

        $this->command->info('Creating job vacancies for employers...');

        $createdJobs = [];

        foreach ($employers as $employer) {
            // Create 1 or 2 job vacancies for each employer
            $numJobs = rand(1, 2);
            for ($j = 0; $j < $numJobs; $j++) {
                $jobTitle = $faker->randomElement($jobTitles);
                $location = $faker->randomElement($locations);
                
                // Create vacancy dates in the last 60 days
                $createdDaysAgo = rand(5, 60);
                $createdAt = now()->subDays($createdDaysAgo);
                
                $job = JobVacancy::create([
                    'employer_id' => $employer->id,
                    'job_title' => $jobTitle,
                    'location' => $location,
                    'description' => "We are looking for a dedicated {$jobTitle} to join our team at {$employer->company_name}. In this role, you will collaborate with teams, perform daily tasks, and grow professionally. We are committed to an inclusive and accessible working environment for all candidates.",
                    'requirements' => $faker->randomElements($skillsPool, rand(2, 4)),
                    'physical_facilities' => $faker->randomElements($physicalPool, rand(1, 2)),
                    'tech_support' => $faker->randomElements($techPool, rand(1, 2)),
                    'work_arrangement' => $faker->randomElements($workPool, rand(1, 2)),
                    'sensory_support' => $faker->randomElements($sensoryPool, rand(1, 2)),
                    'job_status' => $faker->randomElement(['open', 'open', 'open', 'closed']), // 75% open, 25% closed
                    'duration' => $faker->randomElement([30, 60, 90]),
                    'expires_at' => $createdAt->copy()->addDays(30),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                $createdJobs[] = $job;
            }
        }

        $this->command->info('Creating job applications (matches) for PWD users...');

        $pwdProfiles = PwdProfile::all();
        if ($pwdProfiles->isEmpty()) {
            $this->command->error('No PWD profiles found.');
            return;
        }

        $applicationCount = 0;

        foreach ($pwdProfiles as $pwd) {
            // Each PWD candidate applies to 1 to 3 random jobs
            $jobsToApply = $faker->randomElements($createdJobs, rand(1, 3));

            foreach ($jobsToApply as $job) {
                // Ensure no duplicate application
                $existing = JobMatch::where('pwd_id', $pwd->id)
                    ->where('job_id', $job->id)
                    ->exists();

                if ($existing) {
                    continue;
                }

                // Match date is after job creation date
                $jobCreated = Carbon::parse($job->created_at);
                $daysBetween = now()->diffInDays($jobCreated);
                $applyDaysAgo = rand(0, max(0, $daysBetween));
                $applyDate = now()->subDays($applyDaysAgo);

                // Calculate match score using the 5-rule algorithm controller
                $matchData = $pwdController->calculateMatchScore($pwd, $job);

                JobMatch::create([
                    'pwd_id' => $pwd->id,
                    'job_id' => $job->id,
                    'match_score' => $matchData['match_score'],
                    'match_explanation' => [
                        's_score' => $matchData['s_score'],
                        'p_score' => $matchData['p_score'],
                        't_score' => $matchData['t_score'],
                        'w_score' => $matchData['w_score'],
                        'c_score' => $matchData['c_score'],
                        'matched_skills' => $matchData['matched_skills'],
                        'matched_physical' => $matchData['matched_physical'],
                        'matched_tech' => $matchData['matched_tech'],
                        'matched_work' => $matchData['matched_work'],
                        'matched_sensory' => $matchData['matched_sensory'],
                        'missing_skills' => $matchData['missing_skills'],
                        'missing_physical' => $matchData['missing_physical'],
                        'missing_tech' => $matchData['missing_tech'],
                        'missing_work' => $matchData['missing_work'],
                        'missing_sensory' => $matchData['missing_sensory'],
                    ],
                    'application_status' => $faker->randomElement(['pending', 'pending', 'accepted', 'rejected']), // mixed status
                    'created_at' => $applyDate,
                    'updated_at' => $applyDate,
                ]);

                $applicationCount++;
            }
        }

        $this->command->info('Creating QR codes and scan counts for PWD users...');
        foreach ($pwdProfiles as $pwd) {
            $portfolioUrl = url('/api/portfolio/' . $pwd->id);
            $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($portfolioUrl);

            \App\Models\QrCode::updateOrCreate(
                ['pwd_id' => $pwd->id],
                [
                    'qr_image_path' => $qrUrl,
                    'generate_date' => now()->subDays(rand(5, 60)),
                    'scan_count' => rand(5, 50),
                ]
            );
        }

        $this->command->info("Successfully generated " . count($createdJobs) . " job vacancies, {$applicationCount} job applications, and QR codes!");
    }
}
