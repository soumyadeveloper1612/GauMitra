<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLocation extends Model
{
    protected $fillable = [
        'user_id',
        'latitude',
        'longitude',
        'full_address',
        'district',
        'state',
        'is_available',
        'notification_enabled',
        'radius_preference_km',
        'last_seen_at',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'notification_enabled' => 'boolean',
        'last_seen_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}