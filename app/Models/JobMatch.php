<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobMatch extends Model
{
    protected $fillable = [
    'pwd_id', 'job_id', 'match_score',
    'match_explanation', 'application_status'
];

protected $casts = [
    'match_explanation' => 'array',
];

public function pwdProfile()
{
    return $this->belongsTo(PwdProfile::class, 'pwd_id');
}

public function jobVacancy()
{
    return $this->belongsTo(JobVacancy::class, 'job_id');
}
}