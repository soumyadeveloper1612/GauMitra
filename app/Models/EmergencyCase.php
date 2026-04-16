<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmergencyCase extends Model
{
    use SoftDeletes;

    protected $table = 'emergency_cases';

    protected $fillable = [
        'case_uid',
        'reporter_id',
        'current_handler_id',
        'parent_case_id',
        'closed_by',
        'case_type',
        'title',
        'description',
        'severity',
        'cattle_count',
        'contact_number',
        'vehicle_number',
        'vehicle_details',
        'full_address',
        'area_name',
        'land_mark',
        'road_name',
        'city',
        'district',
        'state',
        'pincode',
        'latitude',
        'longitude',
        'status',
        'is_duplicate',
        'notified_radius_km',
        'escalation_level',
        'accepted_at',
        'en_route_at',
        'reached_at',
        'rescue_started_at',
        'resolved_at',
        'closed_at',
        'resolution_notes',
        'false_report_reason',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'en_route_at' => 'datetime',
        'reached_at' => 'datetime',
        'rescue_started_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'is_duplicate' => 'boolean',
    ];

    public const TYPES = [
        'accident',
        'injured_cattle',
        'illegal_transport',
        'abandoned_cattle',
        'dead_cattle',
        'medical_emergency',
        'missing_cattle',
        'rescue_needed',
    ];

    public const SEVERITIES = ['low', 'medium', 'high', 'critical'];

    public const STATUSES = [
        'reported',
        'alerted',
        'accepted',
        'en_route',
        'reached_site',
        'rescue_in_progress',
        'needs_backup',
        'treatment_started',
        'shifted_to_gaushala',
        'resolved',
        'closed',
        'duplicate_case',
        'false_report',
        'unable_to_locate',
        'cancelled',
        'escalated',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function currentHandler()
    {
        return $this->belongsTo(User::class, 'current_handler_id');
    }

    public function media()
    {
        return $this->hasMany(EmergencyCaseMedia::class);
    }

    public function logs()
    {
        return $this->hasMany(EmergencyCaseLog::class);
    }

    public function assignments()
    {
        return $this->hasMany(EmergencyCaseAssignment::class);
    }

    public function alerts()
    {
        return $this->hasMany(EmergencyCaseAlert::class);
    }

    public function scopeOpen($query)
    {
        return $query->whereNotIn('status', [
            'resolved',
            'closed',
            'cancelled',
            'false_report',
            'duplicate_case',
        ]);
    }
}