<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyCaseLog extends Model
{
    protected $table = 'emergency_case_logs';

    protected $fillable = [
        'emergency_case_id',
        'user_id',
        'action',
        'old_status',
        'new_status',
        'notes',
        'latitude',
        'longitude',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
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