<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmployerProfile;
use App\Models\JobVacancy;

class EmployerController extends Controller
{
    public function dashboard(Request $request)
    {
        $this->closeExpiredJobs();
        $user = $request->user();
        $profile = EmployerProfile::where('user_id', $user->id)->first();

        $jobIds = JobVacancy::where('employer_id', $profile?->id)->pluck('id');

        $stats = [
            'totalJobs' => JobVacancy::where('employer_id', $profile?->id)->count(),
            'totalApplications' => \App\Models\JobMatch::whereIn('job_id', $jobIds)->count(),
            'accepted' => \App\Models\JobMatch::whereIn('job_id', $jobIds)->where('application_status', 'accepted')->count(),
            'qrScans' => 0,
        ];

        $jobs = JobVacancy::where('employer_id', $profile?->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(function($job) {
                return [
                    'id' => $job->id,
                    'job_title' => $job->job_title,
                    'location' => $job->location,
                    'job_status' => $job->job_status,
                    'applications_count' => \App\Models\JobMatch::where('job_id', $job->id)->count(),
                ];
            });

        return response()->json([
            'user' => $user,
            'status' => $profile?->status ?? 'pending',
            'stats' => $stats,
            'jobs' => $jobs
        ]);
    }

    public function postJob(Request $request)
    {
        $user = $request->user();
        $profile = EmployerProfile::where('user_id', $user->id)->first();

        if (!$profile) {
            return response()->json(['message' => 'Please complete your company profile first'], 400);
        }

        $expires_at = null;
        if ($request->has('duration') && !is_null($request->duration)) {
            if ($request->duration == 0) {
                $expires_at = now()->addMinutes(2);
            } else {
                $expires_at = now()->addDays($request->duration);
            }
        }

        $job = JobVacancy::create([
            'employer_id'        => $profile->id,
            'job_title'          => $request->job_title,
            'location'           => $request->location,
            'description'        => $request->description,
            'job_status'         => $request->job_status ?? 'open',
            'duration'           => $request->duration,
            'expires_at'         => $expires_at,
            // S-Rule
            'requirements'       => $request->requirements ?? [],
            // P-Rule
            'physical_facilities' => $request->physical_facilities ?? [],
            // T-Rule
            'tech_support'       => $request->tech_support ?? [],
            // W-Rule
            'work_arrangement'   => $request->work_arrangement ?? [],
            // C-Rule
            'sensory_support'    => $request->sensory_support ?? [],
            // Combined for backwards compatibility
            'office_accessibility' => array_merge(
                $request->physical_facilities ?? [],
                $request->tech_support ?? [],
                $request->work_arrangement ?? [],
                $request->sensory_support ?? []
            ),
        ]);

        return response()->json([
            'message' => 'Job posted successfully',
            'job' => $job
        ]);
    }

    public function getJobs(Request $request)
    {
        $this->closeExpiredJobs();
        $user = $request->user();
        $profile = EmployerProfile::where('user_id', $user->id)->first();

        $jobs = JobVacancy::where('employer_id', $profile?->id)
            ->withCount('jobMatches as applications_count')
            ->latest()
            ->get();

        return response()->json(['jobs' => $jobs]);
    }

    public function getJob(Request $request, $id)
    {
        $this->closeExpiredJobs();
        $user = $request->user();
        $profile = EmployerProfile::where('user_id', $user->id)->first();
        $job = JobVacancy::where('id', $id)->where('employer_id', $profile->id)->first();

        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        return response()->json(['job' => $job]);
    }

    public function updateJob(Request $request, $id)
    {
        $user = $request->user();
        $profile = EmployerProfile::where('user_id', $user->id)->first();
        $job = JobVacancy::where('id', $id)->where('employer_id', $profile->id)->first();

        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        $expires_at = $job->expires_at;
        if ($request->has('duration') && $request->duration != $job->duration) {
            if (!is_null($request->duration)) {
                if ($request->duration == 0) {
                    $expires_at = now()->addMinutes(2);
                } else {
                    $expires_at = now()->addDays($request->duration);
                }
            } else {
                $expires_at = null;
            }
        }

        $job->update([
            'job_title'           => $request->job_title,
            'location'            => $request->location,
            'description'         => $request->description,
            'job_status'          => $request->job_status,
            'duration'            => $request->duration,
            'expires_at'          => $expires_at,
            // S-Rule
            'requirements'        => $request->requirements ?? [],
            // P-Rule
            'physical_facilities' => $request->physical_facilities ?? [],
            // T-Rule
            'tech_support'        => $request->tech_support ?? [],
            // W-Rule
            'work_arrangement'    => $request->work_arrangement ?? [],
            // C-Rule
            'sensory_support'     => $request->sensory_support ?? [],
            // Combined
            'office_accessibility' => array_merge(
                $request->physical_facilities ?? [],
                $request->tech_support ?? [],
                $request->work_arrangement ?? [],
                $request->sensory_support ?? []
            ),
        ]);

        return response()->json(['message' => 'Job updated successfully', 'job' => $job]);
    }

    public function deleteJob(Request $request, $id)
    {
        $user = $request->user();
        $profile = EmployerProfile::where('user_id', $user->id)->first();
        $job = JobVacancy::where('id', $id)->where('employer_id', $profile->id)->first();

        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        $job->delete();
        return response()->json(['message' => 'Job deleted successfully']);
    }

    public function getProfile(Request $request)
    {
        $user = $request->user();
        $profile = EmployerProfile::where('user_id', $user->id)->first();

        $stats = [
            'totalJobs' => JobVacancy::where('employer_id', $profile?->id)->count(),
            'openJobs' => JobVacancy::where('employer_id', $profile?->id)->where('job_status', 'open')->count(),
            'totalApplications' => $profile ? \App\Models\JobMatch::whereIn('job_id',
                JobVacancy::where('employer_id', $profile->id)->pluck('id'))->count() : 0,
            'accepted' => $profile ? \App\Models\JobMatch::whereIn('job_id',
                JobVacancy::where('employer_id', $profile->id)->pluck('id'))
                ->where('application_status', 'accepted')->count() : 0,
        ];

        return response()->json([
            'user' => $user,
            'profile' => $profile,
            'stats' => $stats
        ]);
    }

    public function saveProfile(Request $request)
    {
        $user = $request->user();

        EmployerProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'company_name'    => $request->company_name,
                'company_email'   => $request->company_email,
                'contact_number'  => $request->contact_number,
                'ssm_number'      => $request->ssm_number,
                'industry_type'   => $request->industry_type,
                'company_address' => $request->company_address,
                'accessibility'   => $request->accessibility ?? [],
            ]
        );

        $user->update(['name' => $request->name]);

        return response()->json(['message' => 'Profile saved successfully']);
    }

    public function getCandidates(Request $request)
    {
        $candidates = \App\Models\PwdProfile::with('user')
            ->whereIn('status', ['approved', 'pending'])
            ->get()
            ->map(function($p) {
                return [
                    'id'                 => $p->id,
                    'user_id'            => $p->user_id,
                    'full_name'          => $p->full_name,
                    'oku_number'         => $p->oku_number,
                    // S-Rule
                    'skills'             => $p->skills ?? [],
                    // P-Rule
                    'physical_needs'     => $p->physical_needs ?? [],
                    // T-Rule
                    'tech_needs'         => $p->tech_needs ?? [],
                    // W-Rule
                    'work_arrangement'   => $p->work_arrangement ?? [],
                    // C-Rule
                    'sensory_needs'      => $p->sensory_needs ?? [],
                    'certificate_path'   => $p->certificate_path,
                    'video_path'         => $p->video_path,
                    'status'             => $p->status,
                ];
            });

        return response()->json(['candidates' => $candidates]);
    }

    public function getApplications(Request $request)
    {
        $user = $request->user();
        $profile = EmployerProfile::where('user_id', $user->id)->first();

        $jobs = JobVacancy::where('employer_id', $profile?->id)->get();

        $applications = \App\Models\JobMatch::with(['pwdProfile', 'jobVacancy'])
            ->whereIn('job_id', $jobs->pluck('id'))
            ->latest()
            ->get()
            ->map(function($app) {
                $explanation = $app->match_explanation ?? [];
                return [
                    'id'                 => $app->id,
                    'pwd'                => $app->pwdProfile,
                    'job'                => $app->jobVacancy,
                    'job_id'             => $app->job_id,
                    'match_score'        => $app->match_score,
                    // 5-Rule scores
                    's_score'            => $explanation['s_score'] ?? 0,
                    'p_score'            => $explanation['p_score'] ?? 0,
                    't_score'            => $explanation['t_score'] ?? 0,
                    'w_score'            => $explanation['w_score'] ?? 0,
                    'c_score'            => $explanation['c_score'] ?? 0,
                    'application_status' => $app->application_status,
                    'created_at'         => $app->created_at,
                ];
            });

        return response()->json([
            'applications' => $applications,
            'jobs'         => $jobs
        ]);
    }

    public function updateApplicationStatus(Request $request, $id)
    {
        $app = \App\Models\JobMatch::find($id);
        if (!$app) {
            return response()->json(['message' => 'Application not found'], 404);
        }
        $app->update(['application_status' => $request->application_status]);
        return response()->json(['message' => 'Status updated successfully']);
    }

    protected function closeExpiredJobs()
    {
        \App\Models\JobVacancy::where('expires_at', '<=', now())
            ->where('job_status', '!=', 'closed')
            ->update(['job_status' => 'closed']);
    }
}