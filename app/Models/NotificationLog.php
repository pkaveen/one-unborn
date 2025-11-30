<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $fillable = [
        'client_link_id',
        'company_id',
        'notification_type',
        'channel',
        'recipients',
        'message',
        'metadata',
        'sent_successfully',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'recipients' => 'array',
        'metadata' => 'array',
        'sent_successfully' => 'boolean',
        'sent_at' => 'datetime',
    ];

    public function clientLink()
    {
        return $this->belongsTo(ClientLink::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
