<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EmergencyCaseMedia extends Model
{
    protected $table = 'emergency_case_media';

    protected $fillable = [
        'emergency_case_id',
        'user_id',
        'media_type',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
    ];

    protected $appends = [
        'file_url',
    ];

    public function emergencyCase()
    {
        return $this->belongsTo(EmergencyCase::class, 'emergency_case_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getFileUrlAttribute()
    {
        if (!$this->file_path) {
            return null;
        }

        return asset('storage/' . $this->file_path);
    }
}