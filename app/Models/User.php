<?php

namespace App\Models;

use App\Models\DeviceToken;
use App\Models\LoginOtp;
use App\Models\UserAddress;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'mobile',
        'profile_photo',
        'password',
        'status',
        'mobile_verified_at',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'mobile_verified_at' => 'datetime',
        'last_login_at'      => 'datetime',
        'password'           => 'hashed',
    ];

    public function loginOtps()
    {
        return $this->hasMany(LoginOtp::class);
    }

    public function addresses()
    {
        return $this->hasMany(UserAddress::class, 'user_id')
            ->where('status', '!=', 'deleted');
    }

    public function latestAddress()
    {
        return $this->hasOne(UserAddress::class, 'user_id')
            ->where('status', '!=', 'deleted')
            ->latestOfMany();
    }

    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class, 'user_id');
    }

}