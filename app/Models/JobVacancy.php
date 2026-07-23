<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobVacancy extends Model
{
    protected $fillable = [
    'employer_id', 'job_title', 'location', 'description',
    'requirements', 'office_accessibility', 'job_status',
    'physical_facilities', 'tech_support', 'work_arrangement', 'sensory_support',
    'duration', 'expires_at'
];

protected $casts = [
    'requirements' => 'array',
    'office_accessibility' => 'array',
    'physical_facilities' => 'array',
    'tech_support' => 'array',
    'work_arrangement' => 'array',
    'sensory_support' => 'array',
    'expires_at' => 'datetime',
];

    public function employer()
    {
        return $this->belongsTo(EmployerProfile::class, 'employer_id');
    }

    public function jobMatches()
    {
        return $this->hasMany(JobMatch::class, 'job_id');
    }
}