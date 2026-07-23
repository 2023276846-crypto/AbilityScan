<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'admin_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if ($user->role === 'admin' && empty($user->admin_id)) {
                $year = date('Y');
                $latestAdmin = self::where('role', 'admin')
                    ->where('admin_id', 'like', "ADM-$year-%")
                    ->orderBy('id', 'desc')
                    ->first();

                if ($latestAdmin) {
                    $parts = explode('-', $latestAdmin->admin_id);
                    $seq = (int)end($parts) + 1;
                } else {
                    $seq = 1;
                }

                $user->admin_id = sprintf("ADM-%s-%03d", $year, $seq);
            }
        });
    }

    public function pwdProfile()
    {
        return $this->hasOne(PwdProfile::class);
    }

    public function employerProfile()
    {
        return $this->hasOne(EmployerProfile::class);
    }
}