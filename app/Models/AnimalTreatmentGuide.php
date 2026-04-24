<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnimalTreatmentGuide extends Model
{
    protected $fillable = [
        'animal_type',
        'case_type',
        'condition_name',
        'symptoms',
        'first_aid_steps',
        'medicines',
        'dosage',
        'treatment_steps',
        'recovery_steps',
        'precautions',
        'vet_contact_note',
        'priority',
        'status',
        'sort_order',
        'created_by',
    ];
}