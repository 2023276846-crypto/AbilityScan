<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PwdProfile;
use App\Models\EmployerProfile;
use App\Models\JobVacancy;
use App\Models\JobMatch;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $this->closeExpiredJobs();
        $stats = [
            'totalPWD' => PwdProfile::count(),
            'totalEmployers' => EmployerProfile::count(),
            'totalJobs' => JobVacancy::count(),
            'totalApplications' => JobMatch::count(),
            'pendingPWD' => PwdProfile::where('status', 'pending')->count(),
            'pendingEmployers' => EmployerProfile::where('status', 'pending')->count(),
            'approvedPWD' => PwdProfile::where('status', 'approved')->count(),
            'approvedEmployers' => EmployerProfile::where('status', 'approved')->count(),
        ];

        $recentPWD = PwdProfile::with('user')->latest()->take(5)->get();
        $recentEmployers = EmployerProfile::with('user')->latest()->take(5)->get();

        return response()->json([
            'stats' => $stats,
            'recentPWD' => $recentPWD,
            'recentEmployers' => $recentEmployers,
        ]);
    }

    public function getPWDs(Request $request)
    {
        $pwds = PwdProfile::with('user')->latest()->get();
        return response()->json(['pwds' => $pwds]);
    }

    public function updatePWDStatus(Request $request, $id)
    {
        $profile = PwdProfile::find($id);
        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }
        $profile->update(['status' => $request->status]);
        return response()->json(['message' => 'Status updated successfully']);
    }

    public function deletePWD(Request $request, $id)
    {
        $profile = PwdProfile::find($id);
        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }
        User::find($profile->user_id)?->delete();
        return response()->json(['message' => 'PWD deleted successfully']);
    }

    public function getEmployers(Request $request)
    {
        $employers = EmployerProfile::with('user')->latest()->get();
        return response()->json(['employers' => $employers]);
    }

    public function updateEmployerStatus(Request $request, $id)
    {
        $profile = EmployerProfile::find($id);
        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }
        $profile->update(['status' => $request->status]);
        return response()->json(['message' => 'Status updated successfully']);
    }

    public function deleteEmployer(Request $request, $id)
    {
        $profile = EmployerProfile::find($id);
        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }
        User::find($profile->user_id)?->delete();
        return response()->json(['message' => 'Employer deleted successfully']);
    }

    public function getReports(Request $request)
    {
        $reports = [
            'pwdByStatus' => [
                'pending' => PwdProfile::where('status', 'pending')->count(),
                'approved' => PwdProfile::where('status', 'approved')->count(),
                'rejected' => PwdProfile::where('status', 'rejected')->count(),
            ],
            'employerByStatus' => [
                'pending' => EmployerProfile::where('status', 'pending')->count(),
                'approved' => EmployerProfile::where('status', 'approved')->count(),
                'rejected' => EmployerProfile::where('status', 'rejected')->count(),
            ],
            'jobsByStatus' => [
                'open' => JobVacancy::where('job_status', 'open')->count(),
                'closed' => JobVacancy::where('job_status', 'closed')->count(),
            ],
            'applicationsByStatus' => [
                'pending' => JobMatch::where('application_status', 'pending')->count(),
                'accepted' => JobMatch::where('application_status', 'accepted')->count(),
                'rejected' => JobMatch::where('application_status', 'rejected')->count(),
            ],
            'topSkills' => $this->getTopSkills(),
            'topIndustries' => $this->getTopIndustries(),
            'hiringTrends' => $this->getHiringTrends(),
        ];

        return response()->json(['reports' => $reports]);
    }

    private function getTopSkills()
    {
        $skills = [];
        PwdProfile::whereNotNull('skills')->get()->each(function($p) use (&$skills) {
            foreach ($p->skills ?? [] as $skill) {
                $skills[$skill] = ($skills[$skill] ?? 0) + 1;
            }
        });
        arsort($skills);
        return array_slice($skills, 0, 5, true);
    }

    private function getTopIndustries()
    {
        $industries = [];
        EmployerProfile::whereNotNull('industry_type')->get()->each(function($e) use (&$industries) {
            $industries[$e->industry_type] = ($industries[$e->industry_type] ?? 0) + 1;
        });
        arsort($industries);
        return array_slice($industries, 0, 5, true);
    }
    private function getHiringTrends()
    {
        $now = now();
        $since = $now->copy()->subYears(2)->startOfYear();
        
        // Fetch all relevant matches in a single query
        $matches = JobMatch::select('created_at', 'application_status')
            ->where('created_at', '>=', $since)
            ->get();

        // Weekly - last 7 days
        $weekly = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $dateStr = $date->toDateString();
            
            $dayMatches = $matches->filter(function ($m) use ($dateStr) {
                return $m->created_at->toDateString() === $dateStr;
            });
            
            $weekly['labels'][] = $date->format('D');
            $weekly['applications'][] = $dayMatches->count();
            $weekly['accepted'][] = $dayMatches->where('application_status', 'accepted')->count();
        }

        // Monthly - last 12 months
        $monthly = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $year = $date->year;
            $month = $date->month;
            
            $monthMatches = $matches->filter(function ($m) use ($year, $month) {
                return $m->created_at->year === $year && $m->created_at->month === $month;
            });
            
            $monthly['labels'][] = $date->format('M');
            $monthly['applications'][] = $monthMatches->count();
            $monthly['accepted'][] = $monthMatches->where('application_status', 'accepted')->count();
        }

        // Yearly - last 3 years
        $yearly = [];
        for ($i = 2; $i >= 0; $i--) {
            $year = $now->year - $i;
            
            $yearMatches = $matches->filter(function ($m) use ($year) {
                return $m->created_at->year === $year;
            });
            
            $yearly['labels'][] = (string)$year;
            $yearly['applications'][] = $yearMatches->count();
            $yearly['accepted'][] = $yearMatches->where('application_status', 'accepted')->count();
        }

        return [
            'weekly' => $weekly,
            'monthly' => $monthly,
            'yearly' => $yearly,
        ];
    }
public function getMonitor(Request $request)
{
    $this->closeExpiredJobs();
    $totalPWD = PwdProfile::count();
    $totalEmployers = EmployerProfile::count();
    $totalApplications = JobMatch::count();
    $acceptedApplications = JobMatch::where('application_status', 'accepted')->count();
    $rejectedApplications = JobMatch::where('application_status', 'rejected')->count();
    $approvedPWD = PwdProfile::where('status', 'approved')->count();
    $approvedEmployers = EmployerProfile::where('status', 'approved')->count();
    $totalScans = \App\Models\QrCode::sum('scan_count');
    $openJobs = JobVacancy::where('job_status', 'open')->count();
    $highScoreMatches = JobMatch::where('match_score', '>=', 70)->count();
    $avgMatchScore = JobMatch::avg('match_score') ?? 0;

    $health = [
        'matchRate' => $totalPWD > 0 ? round($totalApplications / max($totalPWD, 1), 1) : 0,
        'acceptanceRate' => $totalApplications > 0 ? round(($acceptedApplications / $totalApplications) * 100) : 0,
        'profileCompletion' => $totalPWD > 0 ? round(($approvedPWD / $totalPWD) * 100) : 0,
        'approvalRate' => $totalEmployers > 0 ? round(($approvedEmployers / $totalEmployers) * 100) : 0,
    ];

    $metrics = [
        'totalPWD' => $totalPWD,
        'totalEmployers' => $totalEmployers,
        'openJobs' => $openJobs,
        'totalApplications' => $totalApplications,
        'acceptedApplications' => $acceptedApplications,
        'rejectedApplications' => $rejectedApplications,
        'totalScans' => $totalScans,
        'highScoreMatches' => $highScoreMatches,
        'avgMatchScore' => round($avgMatchScore),
    ];

    $algoStats = [
    'sScore' => $totalApplications > 0 ? round(($acceptedApplications / $totalApplications) * 100) : 0,
    'pScore' => $totalApplications > 0 ? round(($highScoreMatches / max($totalApplications, 1)) * 100) : 0,
    'tScore' => round($avgMatchScore * 0.8),
    'wScore' => round($avgMatchScore * 0.7),
    'cScore' => round($avgMatchScore * 0.6),
    'overallScore' => round($avgMatchScore),
];

    // Recent Activity
    $recentPWD = PwdProfile::with('user')->latest()->take(3)->get()->map(fn($p) => [
        'id' => 'pwd_' . $p->id,
        'icon' => '♿',
        'type' => 'pwd',
        'message' => ($p->full_name ?? 'PWD') . ' registered as PWD',
        'time' => $p->created_at->diffForHumans(),
    ]);

    $recentEmployers = EmployerProfile::with('user')->latest()->take(3)->get()->map(fn($e) => [
        'id' => 'emp_' . $e->id,
        'icon' => '🏢',
        'type' => 'employer',
        'message' => ($e->company_name ?? 'Employer') . ' registered as employer',
        'time' => $e->created_at->diffForHumans(),
    ]);

    $recentApplications = JobMatch::with(['pwdProfile', 'jobVacancy'])->latest()->take(3)->get()->map(fn($a) => [
        'id' => 'app_' . $a->id,
        'icon' => '📋',
        'type' => 'application',
        'message' => ($a->pwdProfile->full_name ?? 'PWD') . ' applied for ' . ($a->jobVacancy->job_title ?? 'a job'),
        'time' => $a->created_at->diffForHumans(),
    ]);

    $recentActivity = collect($recentPWD)
        ->merge($recentEmployers)
        ->merge($recentApplications)
        ->sortByDesc('time')
        ->values()
        ->take(10);

    return response()->json([
        'health' => $health,
        'metrics' => $metrics,
        'algoStats' => $algoStats,
        'recentActivity' => $recentActivity,
    ]);
}

    public function getAlgorithmWeights()
    {
        $weights = \App\Models\AlgorithmWeight::first() ?? [
            's_weight' => 30, 'p_weight' => 25, 't_weight' => 20, 'w_weight' => 15, 'c_weight' => 10
        ];
        return response()->json(['weights' => $weights]);
    }

    public function updateAlgorithmWeights(Request $request)
    {
        $request->validate([
            's_weight' => 'required|numeric|min:0|max:100',
            'p_weight' => 'required|numeric|min:0|max:100',
            't_weight' => 'required|numeric|min:0|max:100',
            'w_weight' => 'required|numeric|min:0|max:100',
            'c_weight' => 'required|numeric|min:0|max:100',
        ]);

        $total = $request->s_weight + $request->p_weight + $request->t_weight + $request->w_weight + $request->c_weight;
        if ($total !== 100) {
            return response()->json(['message' => 'Weights must sum to 100'], 400);
        }

        $weights = \App\Models\AlgorithmWeight::first();
        if (!$weights) {
            $weights = new \App\Models\AlgorithmWeight();
        }

        $weights->s_weight = $request->s_weight;
        $weights->p_weight = $request->p_weight;
        $weights->t_weight = $request->t_weight;
        $weights->w_weight = $request->w_weight;
        $weights->c_weight = $request->c_weight;
        $weights->save();

        \Illuminate\Support\Facades\Cache::forget('algorithm_weights');

        return response()->json(['message' => 'Algorithm weights updated successfully']);
    }

    public function recalculateMatches()
    {
        $matches = \App\Models\JobMatch::all();
        $pwdController = app(\App\Http\Controllers\PWDController::class);
        $updatedCount = 0;

        foreach ($matches as $match) {
            $profile = \App\Models\PwdProfile::find($match->pwd_id);
            $job = \App\Models\JobVacancy::find($match->job_id);
            
            if ($profile && $job) {
                $data = $pwdController->calculateMatchScore($profile, $job);
                $match->update([
                    'match_score' => $data['match_score'],
                    'match_explanation' => [
                        's_score' => $data['s_score'],
                        'p_score' => $data['p_score'],
                        't_score' => $data['t_score'],
                        'w_score' => $data['w_score'],
                        'c_score' => $data['c_score'],
                        'matched_skills' => $data['matched_skills'],
                        'matched_physical' => $data['matched_physical'],
                        'matched_tech' => $data['matched_tech'],
                        'matched_work' => $data['matched_work'],
                        'matched_sensory' => $data['matched_sensory'],
                        'missing_skills' => $data['missing_skills'],
                        'missing_physical' => $data['missing_physical'],
                        'missing_tech' => $data['missing_tech'],
                        'missing_work' => $data['missing_work'],
                        'missing_sensory' => $data['missing_sensory'],
                    ]
                ]);
                $updatedCount++;
            }
        }

        return response()->json(['message' => "Successfully recalculated $updatedCount matches."]);
    }

    protected function closeExpiredJobs()
    {
        \App\Models\JobVacancy::where('expires_at', '<=', now())
            ->where('job_status', '!=', 'closed')
            ->update(['job_status' => 'closed']);
    }
}