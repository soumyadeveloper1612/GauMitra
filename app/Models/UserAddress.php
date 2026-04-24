<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserAddress extends Model
{
    use HasFactory;

    protected $table = 'user_addresses';

    protected $fillable = [
        'user_id',
        'full_address',
        'street',
        'village',
        'police_station',
        'city',
        'district',
        'state',
        'pincode',
        'area_name',
        'latitude',
        'longitude',
        'google_place_id',
        'plus_code',
        'status',
    ];

    protected $casts = [
        'latitude'  => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}