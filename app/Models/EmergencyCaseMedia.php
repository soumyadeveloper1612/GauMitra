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
        if (!$this->file_path) {
            return null;
        }

        if (str_starts_with($this->file_path, 'http://') || str_starts_with($this->file_path, 'https://')) {
            return $this->file_path;
        }

        return asset('storage/' . ltrim($this->file_path, '/'));
    }

    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) {
            return null;
        }

        if ($this->file_size >= 1024 * 1024) {
            return round($this->file_size / (1024 * 1024), 2) . ' MB';
        }

        return round($this->file_size / 1024, 2) . ' KB';
    }
}