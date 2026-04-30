<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyCaseAssignment extends Model
{
    protected $table = 'emergency_case_assignments';

    protected $fillable = [
        'emergency_case_id',
        'user_id',
        'assignment_role',
        'status',
        'distance_km',
        'accepted_at',
        'rejected_at',
        'reached_at',
        'completed_at',
        'cancelled_at',
        'notes',
    ];

    protected $casts = [
        'distance_km'  => 'decimal:2',
        'accepted_at'  => 'datetime',
        'rejected_at'  => 'datetime',
        'reached_at'   => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
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