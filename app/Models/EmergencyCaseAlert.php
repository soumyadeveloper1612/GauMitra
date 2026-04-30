<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyCaseAlert extends Model
{
    use HasFactory;

    protected $table = 'emergency_case_alerts';

    protected $fillable = [
        'emergency_case_id',
        'user_id',
        'fcm_token',
        'platform',
        'area_name',
        'city',
        'district',
        'status',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function emergencyCase()
    {
        return $this->belongsTo(EmergencyCase::class, 'emergency_case_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}