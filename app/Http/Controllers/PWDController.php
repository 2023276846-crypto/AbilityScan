<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PwdProfile;
use App\Models\User;

class PWDController extends Controller
{
    public function getProfile(Request $request)
    {
        $user = $request->user();
        $profile = PwdProfile::where('user_id', $user->id)->first();
        return response()->json([
            'user' => $user,
            'profile' => $profile
        ]);
    }

    public function saveProfile(Request $request)
    {
        $user = $request->user();

        $profile = PwdProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'full_name' => $request->full_name,
                'oku_number' => $request->oku_number,
                'skills' => json_decode($request->skills, true),
                'physical_needs' => json_decode($request->physical_needs ?? '[]', true),
                'tech_needs' => json_decode($request->tech_needs ?? '[]', true),
                'work_arrangement' => json_decode($request->work_arrangement ?? '[]', true),
                'sensory_needs' => json_decode($request->sensory_needs ?? '[]', true),
                'accessibility_needs' => json_decode($request->accessibility_needs ?? '[]', true),
                'location' => $request->location,
                'about_me' => $request->about_me,
                'experience' => $request->experience,
                'education' => json_decode($request->education ?? '[]', true),
            ]
        );

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $profile->update(['avatar_path' => $path]);
        }

        // Handle OKU card upload
        if ($request->hasFile('oku_card')) {
            $path = $request->file('oku_card')->store('oku_cards', 'public');
            $profile->update(['oku_card_path' => $path]);
        }

        // Handle multiple certificates upload
        $existingCertificates = json_decode($request->existing_certificates ?? '[]', true);
        $certificates = $existingCertificates;
        if ($request->hasFile('new_certificates')) {
            foreach ($request->file('new_certificates') as $file) {
                $path = $file->store('certificates', 'public');
                $certificates[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path
                ];
            }
        }
        // Handle legacy single certificate upload
        if ($request->hasFile('certificate')) {
            $path = $request->file('certificate')->store('certificates', 'public');
            $certificates[] = [
                'name' => $request->file('certificate')->getClientOriginalName(),
                'path' => $path
            ];
        }

        // Handle multiple videos upload
        $existingVideos = json_decode($request->existing_videos ?? '[]', true);
        $videos = $existingVideos;
        if ($request->hasFile('new_videos')) {
            foreach ($request->file('new_videos') as $file) {
                $path = $file->store('videos', 'public');
                $videos[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path
                ];
            }
        }
        // Handle legacy single video upload
        if ($request->hasFile('video')) {
            $path = $request->file('video')->store('videos', 'public');
            $videos[] = [
                'name' => $request->file('video')->getClientOriginalName(),
                'path' => $path
            ];
        }

        $profile->update([
            'certificates' => $certificates,
            'videos' => $videos,
            'certificate_path' => isset($certificates[0]) ? $certificates[0]['path'] : null,
            'video_path' => isset($videos[0]) ? $videos[0]['path'] : null,
        ]);

        return response()->json([
            'message' => 'Profile saved successfully',
            'profile' => $profile
        ]);
    }

    public function getQR(Request $request)
    {
        $user = $request->user();
        $profile = PwdProfile::where('user_id', $user->id)->first();
        if (!$profile) {
            return response()->json(['qr_url' => null]);
        }
        $qr = \App\Models\QrCode::where('pwd_id', $profile->id)->first();
        if (!$qr) {
            return response()->json(['qr_url' => null]);
        }
        return response()->json([
            'qr_url' => $qr->qr_image_path,
            'qr_info' => [
                'generate_date' => $qr->generate_date,
                'scan_count' => $qr->scan_count
            ]
        ]);
    }

    public function generateQR(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'User not authenticated'], 401);
            }

            $profile = PwdProfile::where('user_id', $user->id)->first();

            if (!$profile) {
                return response()->json([
                    'message' => 'Profile not found for user id: ' . $user->id
                ], 400);
            }

            $portfolioUrl = url('/api/portfolio/' . $profile->id);
            $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($portfolioUrl);

            $qr = \App\Models\QrCode::updateOrCreate(
                ['pwd_id' => $profile->id],
                [
                    'qr_image_path' => $qrUrl,
                    'generate_date' => now(),
                ]
            );

            $profile->update(['digital_portfolio_link' => $portfolioUrl]);

            return response()->json([
                'qr_url' => $qrUrl,
                'qr_info' => [
                    'generate_date' => $qr->generate_date,
                    'scan_count' => $qr->scan_count
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function publicPortfolio($id)
{
    $profile = PwdProfile::find($id);
    if (!$profile) {
        return response()->json(['message' => 'Portfolio not found'], 404);
    }

    $qr = \App\Models\QrCode::where('pwd_id', $profile->id)->first();
    if ($qr) {
        $qr->increment('scan_count');
    }

    $user = User::find($profile->user_id);

    return response()->json([
        'profile' => [
            'id'               => $profile->id,
            'user_id'          => $profile->user_id,
            'full_name'        => $profile->full_name,
            'oku_number'       => $profile->oku_number,
            'skills'           => $profile->skills ?? [],
            'physical_needs'   => $profile->physical_needs ?? [],
            'tech_needs'       => $profile->tech_needs ?? [],
            'work_arrangement' => $profile->work_arrangement ?? [],
            'sensory_needs'    => $profile->sensory_needs ?? [],
            'certificate_path' => $profile->certificate_path,
            'video_path'       => $profile->video_path,
            'status'           => $profile->status,
            'email'            => $user->email ?? '',
            'avatar_path'      => $profile->avatar_path,
            'location'         => $profile->location,
            'about_me'         => $profile->about_me,
            'experience'       => $profile->experience,
            'education'        => $profile->education ?? [],
            'certificates'     => $profile->certificates ?? [],
            'videos'           => $profile->videos ?? [],
        ]
    ]);
}

    // =====================
    // 5-RULE ALGORITHM
    // S(30%) + P(25%) + T(20%) + W(15%) + C(10%)
    // =====================
    public function calculateMatchScore($pwdProfile, $job)
    {
        // S-RULE: Skills Match (Weight: 30%)
        $pwdSkills = $pwdProfile->skills ?? [];
        $jobRequirements = $job->requirements ?? [];
        $matchedSkills = [];
        $missingSkills = [];

        if (count($jobRequirements) > 0) {
            $matchedSkills = array_values(array_intersect($pwdSkills, $jobRequirements));
            $missingSkills = array_values(array_diff($jobRequirements, $pwdSkills));
            $sScore = round((count($matchedSkills) / count($jobRequirements)) * 100);
        } else {
            $sScore = 100;
        }

        // P-RULE: Physical Accessibility Match (Weight: 25%)
        $pwdPhysical = $pwdProfile->physical_needs ?? [];
        $jobPhysical = $job->physical_facilities ?? [];
        $matchedPhysical = [];
        $missingPhysical = [];

        if (count($pwdPhysical) > 0) {
            $matchedPhysical = array_values(array_intersect($pwdPhysical, $jobPhysical));
            $missingPhysical = array_values(array_diff($pwdPhysical, $jobPhysical));
            $pScore = round((count($matchedPhysical) / count($pwdPhysical)) * 100);
        } else {
            $pScore = 100;
        }

        // T-RULE: Technology Support Match (Weight: 20%)
        $pwdTech = $pwdProfile->tech_needs ?? [];
        $jobTech = $job->tech_support ?? [];
        $matchedTech = [];
        $missingTech = [];

        if (count($pwdTech) > 0) {
            $matchedTech = array_values(array_intersect($pwdTech, $jobTech));
            $missingTech = array_values(array_diff($pwdTech, $jobTech));
            $tScore = round((count($matchedTech) / count($pwdTech)) * 100);
        } else {
            $tScore = 100;
        }

        // W-RULE: Work Arrangement Match (Weight: 15%)
        $pwdWork = $pwdProfile->work_arrangement ?? [];
        $jobWork = $job->work_arrangement ?? [];
        $matchedWork = [];
        $missingWork = [];

        if (count($pwdWork) > 0) {
            $matchedWork = array_values(array_intersect($pwdWork, $jobWork));
            $missingWork = array_values(array_diff($pwdWork, $jobWork));
            $wScore = round((count($matchedWork) / count($pwdWork)) * 100);
        } else {
            $wScore = 100;
        }

        // C-RULE: Communication & Sensory Match (Weight: 10%)
        $pwdSensory = $pwdProfile->sensory_needs ?? [];
        $jobSensory = $job->sensory_support ?? [];
        $matchedSensory = [];
        $missingSensory = [];

        if (count($pwdSensory) > 0) {
            $matchedSensory = array_values(array_intersect($pwdSensory, $jobSensory));
            $missingSensory = array_values(array_diff($pwdSensory, $jobSensory));
            $cScore = round((count($matchedSensory) / count($pwdSensory)) * 100);
        } else {
            $cScore = 100;
        }

        // Fetch dynamic weights or use defaults
        $weights = \Illuminate\Support\Facades\Cache::remember('algorithm_weights', 60, function () {
            return \App\Models\AlgorithmWeight::first() ?? new \App\Models\AlgorithmWeight([
                's_weight' => 30, 'p_weight' => 25, 't_weight' => 20, 'w_weight' => 15, 'c_weight' => 10
            ]);
        });

        // FINAL SCORE: S(W%) + P(W%) + T(W%) + W(W%) + C(W%)
        $finalScore = round(
            ($sScore * ($weights->s_weight / 100)) +
            ($pScore * ($weights->p_weight / 100)) +
            ($tScore * ($weights->t_weight / 100)) +
            ($wScore * ($weights->w_weight / 100)) +
            ($cScore * ($weights->c_weight / 100))
        );

        return [
            'match_score'          => $finalScore,
            's_score'              => $sScore,
            'p_score'              => $pScore,
            't_score'              => $tScore,
            'w_score'              => $wScore,
            'c_score'              => $cScore,
            'matched_skills'       => $matchedSkills,
            'matched_physical'     => $matchedPhysical,
            'matched_tech'         => $matchedTech,
            'matched_work'         => $matchedWork,
            'matched_sensory'      => $matchedSensory,
            'missing_skills'       => $missingSkills,
            'missing_physical'     => $missingPhysical,
            'missing_tech'         => $missingTech,
            'missing_work'         => $missingWork,
            'missing_sensory'      => $missingSensory,
        ];
    }

    public function getJobs(Request $request)
    {
        $this->closeExpiredJobs();
        $user = $request->user();
        $profile = PwdProfile::where('user_id', $user->id)->first();

        $jobs = \App\Models\JobVacancy::with('employer')
            ->where('job_status', 'open')
            ->get()
            ->map(function($job) use ($profile) {
                $matchData = [
                    'match_score' => 0,
                    's_score' => 0, 'p_score' => 0, 't_score' => 0,
                    'w_score' => 0, 'c_score' => 0,
                    'matched_skills' => [], 'matched_physical' => [],
                    'matched_tech' => [], 'matched_work' => [], 'matched_sensory' => [],
                    'missing_skills' => [], 'missing_physical' => [],
                    'missing_tech' => [], 'missing_work' => [], 'missing_sensory' => [],
                ];

                if ($profile) {
                    $matchData = $this->calculateMatchScore($profile, $job);
                }

                return [
                    'id' => $job->id,
                    'job_title' => $job->job_title,
                    'location' => $job->location,
                    'description' => $job->description,
                    'requirements' => $job->requirements ?? [],
                    'physical_facilities' => $job->physical_facilities ?? [],
                    'tech_support' => $job->tech_support ?? [],
                    'work_arrangement' => $job->work_arrangement ?? [],
                    'sensory_support' => $job->sensory_support ?? [],
                    'office_accessibility' => $job->office_accessibility ?? [],
                    'job_status' => $job->job_status,
                    'employer' => $job->employer,
                    'match_score' => $matchData['match_score'],
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
                ];
            })
            ->sortByDesc('match_score')
            ->values();

        return response()->json(['jobs' => $jobs]);
    }

    public function applyJob(Request $request)
    {
        $user = $request->user();
        $profile = PwdProfile::where('user_id', $user->id)->first();

        if (!$profile) {
            return response()->json(['message' => 'Please complete your profile first'], 400);
        }

        $existing = \App\Models\JobMatch::where('pwd_id', $profile->id)
            ->where('job_id', $request->job_id)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Already applied'], 400);
        }

        $job = \App\Models\JobVacancy::find($request->job_id);

        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        $matchData = $this->calculateMatchScore($profile, $job);

        \App\Models\JobMatch::create([
            'pwd_id' => $profile->id,
            'job_id' => $request->job_id,
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
            'application_status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Application sent successfully',
            'match_score' => $matchData['match_score'],
            's_score' => $matchData['s_score'],
            'p_score' => $matchData['p_score'],
            't_score' => $matchData['t_score'],
            'w_score' => $matchData['w_score'],
            'c_score' => $matchData['c_score'],
        ]);
    }

    public function getMessages(Request $request)
    {
        return response()->json(['messages' => []]);
    }

    public function getApplications(Request $request)
    {
        $user = $request->user();
        $profile = PwdProfile::where('user_id', $user->id)->first();

        if (!$profile) {
            return response()->json(['applications' => []]);
        }

        $applications = \App\Models\JobMatch::with(['jobVacancy.employer'])
            ->where('pwd_id', $profile->id)
            ->latest()
            ->get()
            ->map(function($app) {
                $explanation = $app->match_explanation ?? [];
                return [
                    'id' => $app->id,
                    'job' => $app->jobVacancy,
                    'match_score' => $app->match_score,
                    's_score' => $explanation['s_score'] ?? 0,
                    'p_score' => $explanation['p_score'] ?? 0,
                    't_score' => $explanation['t_score'] ?? 0,
                    'w_score' => $explanation['w_score'] ?? 0,
                    'c_score' => $explanation['c_score'] ?? 0,
                    'application_status' => $app->application_status,
                    'created_at' => $app->created_at,
                ];
            });

        return response()->json(['applications' => $applications]);
    }

    public function getDashboard(Request $request)
    {
        $this->closeExpiredJobs();
        $user = $request->user();
        $profile = PwdProfile::where('user_id', $user->id)->first();

        $stats = [
            'totalApplications' => 0,
            'accepted' => 0,
            'pending' => 0,
            'rejected' => 0,
        ];

        if ($profile) {
            $stats['totalApplications'] = \App\Models\JobMatch::where('pwd_id', $profile->id)->count();
            $stats['accepted'] = \App\Models\JobMatch::where('pwd_id', $profile->id)->where('application_status', 'accepted')->count();
            $stats['pending'] = \App\Models\JobMatch::where('pwd_id', $profile->id)->where('application_status', 'pending')->count();
            $stats['rejected'] = \App\Models\JobMatch::where('pwd_id', $profile->id)->where('application_status', 'rejected')->count();
        }

        return response()->json([
            'user' => $user,
            'profile' => $profile,
            'stats' => $stats
        ]);
    }

    public function getJob(Request $request, $id)
    {
        $this->closeExpiredJobs();
        $user = $request->user();
        $profile = PwdProfile::where('user_id', $user->id)->first();
        $job = \App\Models\JobVacancy::with('employer')->find($id);

        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        $matchData = [
            'match_score' => 0,
            's_score' => 0, 'p_score' => 0, 't_score' => 0,
            'w_score' => 0, 'c_score' => 0,
            'matched_skills' => [], 'matched_physical' => [],
            'matched_tech' => [], 'matched_work' => [], 'matched_sensory' => [],
            'missing_skills' => [], 'missing_physical' => [],
            'missing_tech' => [], 'missing_work' => [], 'missing_sensory' => [],
        ];

        if ($profile) {
            $matchData = $this->calculateMatchScore($profile, $job);
        }

        return response()->json([
            'job' => $job,
            'match' => $matchData,
            'profile' => $profile,
        ]);
    }

    public function searchPortfolio(Request $request)
{
    $q = trim($request->query('q', ''));

    if (!$q) {
        return response()->json(['message' => 'Search query required'], 400);
    }

    $profile = null;

    // Search by OKU number first (exact match)
    $profile = PwdProfile::where('oku_number', $q)->first();

    // If not found and input is numeric, search by ID
    if (!$profile && is_numeric($q)) {
        $profile = PwdProfile::find((int)$q);
    }

    if (!$profile) {
        return response()->json(['message' => 'Portfolio not found'], 404);
    }

    // Increment scan count
    $qr = \App\Models\QrCode::where('pwd_id', $profile->id)->first();
    if ($qr) {
        $qr->increment('scan_count');
    }

    $user = User::find($profile->user_id);

    return response()->json([
        'profile' => [
            'id'               => $profile->id,
            'user_id'          => $profile->user_id,
            'full_name'        => $profile->full_name,
            'oku_number'       => $profile->oku_number,
            'skills'           => $profile->skills ?? [],
            'physical_needs'   => $profile->physical_needs ?? [],
            'tech_needs'       => $profile->tech_needs ?? [],
            'work_arrangement' => $profile->work_arrangement ?? [],
            'sensory_needs'    => $profile->sensory_needs ?? [],
            'certificate_path' => $profile->certificate_path,
            'video_path'       => $profile->video_path,
            'status'           => $profile->status,
            'email'            => $user->email ?? '',
            'avatar_path'      => $profile->avatar_path,
            'location'         => $profile->location,
            'about_me'         => $profile->about_me,
            'experience'       => $profile->experience,
            'education'        => $profile->education ?? [],
            'certificates'     => $profile->certificates ?? [],
            'videos'           => $profile->videos ?? [],
        ]
    ]);
}

    protected function closeExpiredJobs()
    {
        \App\Models\JobVacancy::where('expires_at', '<=', now())
            ->where('job_status', '!=', 'closed')
            ->update(['job_status' => 'closed']);
    }
}