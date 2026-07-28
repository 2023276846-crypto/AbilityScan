<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PwdProfile extends Model
{
    use HasFactory;
    protected $fillable = [
    'user_id', 'full_name', 'oku_number', 'oku_card_path',
    'certificate_path', 'video_path', 'skills',
    'physical_needs', 'tech_needs', 'work_arrangement', 'sensory_needs',
    'accessibility_needs', 'digital_portfolio_link', 'status',
    'avatar_path', 'location', 'about_me', 'experience', 'education',
    'certificates', 'videos'
];

protected $casts = [
    'skills' => 'array',
    'physical_needs' => 'array',
    'tech_needs' => 'array',
    'work_arrangement' => 'array',
    'sensory_needs' => 'array',
    'accessibility_needs' => 'array',
    'education' => 'array',
    'certificates' => 'array',
    'videos' => 'array',
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function qrCode()
    {
        return $this->hasOne(QrCode::class, 'pwd_id');
    }

    public function jobMatches()
    {
        return $this->hasMany(JobMatch::class, 'pwd_id');
    }
}