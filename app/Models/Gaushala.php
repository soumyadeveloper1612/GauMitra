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
        'latitude',
        'longitude',
        'status',
    ];
}