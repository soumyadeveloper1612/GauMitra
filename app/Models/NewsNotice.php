<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsNotice extends Model
{
    protected $table = 'news_notices';

    protected $fillable = [
        'category',
        'title',
        'short_description',
        'description',
        'notice_date',
        'location',
        'contact_person',
        'contact_number',
        'priority',
        'status',
    ];

    protected $casts = [
        'notice_date' => 'date',
    ];

    public static function categoryOptions(): array
    {
        return [
            'rescue_success_stories'            => 'Rescue Success Stories',
            'local_notices'                     => 'Local Notices',
            'legal_awareness'                   => 'Legal Awareness',
            'gaushala_requirements'             => 'Gaushala Requirements',
            'missing_cattle'                    => 'Missing Cattle',
            'adoption_foster_updates'           => 'Adoption / Foster Updates',
            'emergency_weather_alerts'          => 'Emergency Weather Alerts',
            'government_animal_welfare_schemes' => 'Government Animal Welfare Schemes',
        ];
    }

    public static function priorityOptions(): array
    {
        return [
            'low'    => 'Low',
            'medium' => 'Medium',
            'high'   => 'High',
            'urgent' => 'Urgent',
        ];
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::categoryOptions()[$this->category] ?? ucwords(str_replace('_', ' ', $this->category));
    }
}