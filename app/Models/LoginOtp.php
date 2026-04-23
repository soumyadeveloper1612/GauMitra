<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoginOtp extends Model
{
    use HasFactory;

    protected $table = 'login_otps';

    protected $fillable = [
        'user_id',
        'mobile',
        'platform',
        'device_id',
        'purpose',
        'otp_hash',
        'expires_at',
        'verified_at',
        'is_used',
        'attempts',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'expires_at'  => 'datetime',
        'verified_at' => 'datetime',
        'is_used'     => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}