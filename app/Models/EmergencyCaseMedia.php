<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'formatted_file_size',
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
        if (empty($this->file_path)) {
            return null;
        }

        $path = ltrim($this->file_path, '/');

        if (preg_match('/^https?:\/\//', $path)) {
            return $path;
        }

        return asset('storage/' . $path);
    }

    public function getFormattedFileSizeAttribute()
    {
        if (empty($this->file_size)) {
            return null;
        }

        $size = (int) $this->file_size;

        if ($size >= 1024 * 1024) {
            return round($size / (1024 * 1024), 2) . ' MB';
        }

        return round($size / 1024, 2) . ' KB';
    }
}