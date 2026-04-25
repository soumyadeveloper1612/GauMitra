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

    protected $appends = [
        'notification_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getNotificationTokenAttribute()
    {
        if (!empty($this->fcm_token)) {
            return $this->fcm_token;
        }

        return $this->device_id;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithNotificationToken($query)
    {
        return $query->where(function ($q) {
            $q->where(function ($sub) {
                $sub->whereNotNull('fcm_token')
                    ->where('fcm_token', '!=', '');
            })->orWhere(function ($sub) {
                $sub->whereNotNull('device_id')
                    ->where('device_id', '!=', '');
            });
        });
    }

    public function scopeWithFcmToken($query)
    {
        return $query->withNotificationToken();
    }

    public function scopePlatform($query, ?string $platform)
    {
        if (!empty($platform)) {
            return $query->where('platform', $platform);
        }

        return $query;
    }

    public function scopeDevice($query, ?string $deviceId)
    {
        if (!empty($deviceId)) {
            return $query->where('device_id', $deviceId);
        }

        return $query;
    }
}