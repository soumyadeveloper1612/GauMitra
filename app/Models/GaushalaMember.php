<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GaushalaMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'gaushala_id',
        'member_name',
        'member_phone',
        'status',
    ];

    public function gaushala()
    {
        return $this->belongsTo(Gaushala::class, 'gaushala_id');
    }
}