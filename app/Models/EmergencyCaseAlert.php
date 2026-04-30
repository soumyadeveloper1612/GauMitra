<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyCaseAlert extends Model
{
    protected $table = 'emergency_case_alerts';

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
        'radius_km'    => 'decimal:2',
        'distance_km'  => 'decimal:2',
        'sent_at'      => 'datetime',
        'seen_at'      => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function emergencyCase()
    {
        return $this->belongsTo(EmergencyCase::class, 'emergency_case_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function deviceToken()
    {
        return $this->belongsTo(DeviceToken::class, 'device_token_id');
    }
}