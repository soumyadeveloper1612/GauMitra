<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    use HasFactory;

    protected $table = 'device_tokens';

    protected $fillable = [
        'user_id',
        'platform',
        'device_id',
        'fcm_token',
        'is_active',
        'last_used_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'last_used_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithFcmToken($query)
    {
        return $query
            ->whereNotNull('fcm_token')
            ->where('fcm_token', '!=', '');
    }

    public function scopePlatform($query, ?string $platform)
    {
        if (!empty($platform)) {
            return $query->where('platform', $platform);
        }

        return $query;
    }
}