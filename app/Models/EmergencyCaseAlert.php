<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyCaseAlert extends Model
{
    protected $fillable = [
        'emergency_case_id',
        'user_id',
        'device_token_id',
        'notification_type',
        'radius_km',
        'distance_km',
        'status',
        'sent_at',
        'seen_at',
        'responded_at',
        'response',
        'error_message',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'seen_at' => 'datetime',
        'responded_at' => 'datetime',
    ];
}