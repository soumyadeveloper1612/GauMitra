<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gaushala extends Model
{
    use HasFactory;

    protected $fillable = [
        'gaushala_name',
        'owner_manager_name',
        'mobile_number',
        'alternate_number',
        'full_address',
        'district',
        'state',
        'total_capacity',
        'available_capacity',
        'rescue_vehicle',
        'doctor',
        'food_support',
        'temporary_shelter',
        'gaushala_photo',
        'registration_proof',
        'working_hours',
        'emergency_availability',
        'status',
    ];

    protected $casts = [
        'rescue_vehicle'    => 'boolean',
        'doctor'            => 'boolean',
        'food_support'      => 'boolean',
        'temporary_shelter' => 'boolean',
    ];

    public function members()
    {
        return $this->hasMany(GaushalaMember::class, 'gaushala_id');
    }

    public function activeMembers()
    {
        return $this->hasMany(GaushalaMember::class, 'gaushala_id')
            ->where('status', 'active');
    }
}