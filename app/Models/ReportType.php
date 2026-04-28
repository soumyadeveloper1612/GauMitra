<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReportType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon_class',
        'color_code',
        'description',
        'sort_order',
        'status',
    ];

    public function animalConditions()
    {
        return $this->hasMany(AnimalCondition::class, 'report_type_id');
    }

    public function activeAnimalConditions()
    {
        return $this->hasMany(AnimalCondition::class, 'report_type_id')
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->orderBy('name');
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