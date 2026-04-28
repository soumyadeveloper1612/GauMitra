<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AnimalCondition extends Model
{
    use HasFactory;

    protected $table = 'animal_conditions';

    protected $fillable = [
        'report_type_id',
        'name',
        'slug',
        'severity_level',
        'icon_class',
        'color_code',
        'symptoms',
        'first_aid_steps',
        'description',
        'sort_order',
        'status',
    ];

    public function reportType()
    {
        return $this->belongsTo(ReportType::class, 'report_type_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeNotDeleted($query)
    {
        return $query->where('status', '!=', 'deleted');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}