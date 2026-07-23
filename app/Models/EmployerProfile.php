<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployerProfile extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'company_name', 'company_address',
        'industry_type', 'ssm_number', 'contact_number',
        'company_email', 'accessibility', 'status'
    ];

    protected $casts = [
        'accessibility' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobVacancies()
    {
        return $this->hasMany(JobVacancy::class, 'employer_id');
    }
}