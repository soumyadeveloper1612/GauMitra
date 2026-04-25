<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationCampaign extends Model
{
    protected $fillable = [
        'notification_type',
        'title',
        'message',
        'target_scope',
        'target_filters',
        'image_url',
        'action_url',
        'related_type',
        'related_id',
        'status',
        'total_users',
        'total_devices',
        'success_count',
        'failure_count',
        'error_message',
        'sent_by',
        'sent_at',
    ];

    protected $casts = [
        'target_filters' => 'array',
        'sent_at' => 'datetime',
    ];

    public function recipients()
    {
        return $this->hasMany(NotificationRecipient::class);
    }
}