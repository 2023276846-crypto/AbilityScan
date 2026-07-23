<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    protected $fillable = [
        'pwd_id', 'qr_image_path',
        'scan_count', 'generate_date'
    ];

    public function pwdProfile()
    {
        return $this->belongsTo(PwdProfile::class, 'pwd_id');
    }
}