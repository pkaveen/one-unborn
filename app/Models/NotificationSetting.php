<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    protected $fillable = [
        'company_id',
        'sla_breach_enabled',
        'sla_breach_recipients',
        'sla_breach_to_client',
        'sla_breach_to_operations',
        'link_down_enabled',
        'link_down_threshold_minutes',
        'link_down_recipients',
        'high_latency_enabled',
        'high_latency_threshold_ms',
        'high_latency_duration_minutes',
        'high_latency_recipients',
        'high_packet_loss_enabled',
        'high_packet_loss_threshold_percent',
        'high_packet_loss_duration_minutes',
        'high_packet_loss_recipients',
        'whatsapp_enabled',
        'whatsapp_numbers',
        'email_enabled',
        'email_from',
        'alert_cooldown_minutes',
    ];

    protected $casts = [
        'sla_breach_enabled' => 'boolean',
        'sla_breach_recipients' => 'array',
        'sla_breach_to_client' => 'boolean',
        'sla_breach_to_operations' => 'boolean',
        'link_down_enabled' => 'boolean',
        'link_down_threshold_minutes' => 'integer',
        'link_down_recipients' => 'array',
        'high_latency_enabled' => 'boolean',
        'high_latency_threshold_ms' => 'integer',
        'high_latency_duration_minutes' => 'integer',
        'high_latency_recipients' => 'array',
        'high_packet_loss_enabled' => 'boolean',
        'high_packet_loss_threshold_percent' => 'decimal:2',
        'high_packet_loss_duration_minutes' => 'integer',
        'high_packet_loss_recipients' => 'array',
        'whatsapp_enabled' => 'boolean',
        'whatsapp_numbers' => 'array',
        'email_enabled' => 'boolean',
        'alert_cooldown_minutes' => 'integer',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get default recipients for operations team
     */
    public function getOperationsEmails()
    {
        // Get users from Operations team (user_type_id or specific role)
        return \App\Models\User::where('company_id', $this->company_id)
            ->whereIn('user_type_id', [1, 2]) // Superadmin, Admin
            ->pluck('email')
            ->toArray();
    }

    /**
     * Check if notification was sent recently (cooldown period)
     */
    public function isInCooldown($linkId, $notificationType)
    {
        $cooldownMinutes = $this->alert_cooldown_minutes ?? 30;
        
        $recentNotification = NotificationLog::where('client_link_id', $linkId)
            ->where('notification_type', $notificationType)
            ->where('sent_successfully', true)
            ->where('created_at', '>=', now()->subMinutes($cooldownMinutes))
            ->exists();

        return $recentNotification;
    }
}
