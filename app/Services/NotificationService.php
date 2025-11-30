<?php

namespace App\Services;

use App\Models\ClientLink;
use App\Models\NotificationSetting;
use App\Models\NotificationLog;
use App\Models\ClientPortalUser;
use App\Models\LinkMonitoringData;
use App\Mail\SlaBreachNotification;
use App\Mail\LinkDownAlert;
use App\Mail\HighLatencyAlert;
use App\Mail\HighPacketLossAlert;
use App\Helpers\WhatsAppHelper;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send SLA breach notification (if enabled)
     */
    public function sendSlaBreachNotification($slaReport)
    {
        $link = $slaReport->clientLink;
        $settings = NotificationSetting::where('company_id', $link->client->company_id)->first();

        if (!$settings || !$settings->sla_breach_enabled) {
            Log::info("SLA breach notifications disabled for company {$link->client->company_id}");
            return;
        }

        // Check cooldown
        if ($settings->isInCooldown($link->id, 'sla_breach')) {
            Log::info("SLA breach notification in cooldown for link {$link->id}");
            return;
        }

        $recipients = [];

        // Add client portal users if enabled
        if ($settings->sla_breach_to_client) {
            $clientEmails = ClientPortalUser::where('client_id', $link->client_id)
                ->where('status', 'active')
                ->pluck('email')
                ->toArray();
            $recipients = array_merge($recipients, $clientEmails);
        }

        // Add operations team if enabled
        if ($settings->sla_breach_to_operations) {
            $recipients = array_merge($recipients, $settings->getOperationsEmails());
        }

        // Add custom recipients
        if ($settings->sla_breach_recipients) {
            $recipients = array_merge($recipients, $settings->sla_breach_recipients);
        }

        $recipients = array_unique(array_filter($recipients));

        if (empty($recipients)) {
            Log::warning("No recipients configured for SLA breach notifications");
            return;
        }

        // Send emails
        if ($settings->email_enabled) {
            $this->sendEmailNotification(
                $recipients,
                new SlaBreachNotification($slaReport),
                $link->id,
                'sla_breach',
                $link->client->company_id
            );
        }

        // Send WhatsApp if enabled
        if ($settings->whatsapp_enabled && $settings->whatsapp_numbers) {
            $this->sendWhatsAppAlert(
                $settings->whatsapp_numbers,
                $this->formatSlaBreachWhatsApp($slaReport),
                $link->id,
                'sla_breach',
                $link->client->company_id
            );
        }
    }

    /**
     * Check and send link down alert
     */
    public function checkLinkDownAlert(ClientLink $link, LinkMonitoringData $latestData)
    {
        $settings = NotificationSetting::where('company_id', $link->client->company_id)->first();

        if (!$settings || !$settings->link_down_enabled) {
            return;
        }

        if ($latestData->interface_status !== 'down') {
            return;
        }

        // Check if down for threshold duration
        $thresholdMinutes = $settings->link_down_threshold_minutes ?? 5;
        $downSince = LinkMonitoringData::where('client_link_id', $link->id)
            ->where('interface_status', 'down')
            ->where('collected_at', '>=', now()->subMinutes($thresholdMinutes))
            ->orderBy('collected_at', 'asc')
            ->first();

        if (!$downSince) {
            return; // Not down long enough
        }

        // Check cooldown
        if ($settings->isInCooldown($link->id, 'link_down')) {
            return;
        }

        $recipients = array_merge(
            $settings->link_down_recipients ?? [],
            $settings->getOperationsEmails()
        );
        $recipients = array_unique(array_filter($recipients));

        $message = sprintf(
            "🔴 LINK DOWN ALERT\n\nLink: %s\nRouter: %s\nInterface: %s\nDown since: %s (%d minutes)\n\nImmediate action required!",
            $link->deliverable->deliverable_id ?? 'N/A',
            $link->router->name,
            $link->interface_name,
            $downSince->collected_at->format('Y-m-d H:i:s'),
            $downSince->collected_at->diffInMinutes(now())
        );

        // Send email
        if ($settings->email_enabled && !empty($recipients)) {
            $this->sendEmailNotification(
                $recipients,
                new LinkDownAlert($link, $downSince->collected_at),
                $link->id,
                'link_down',
                $link->client->company_id
            );
        }

        // Send WhatsApp
        if ($settings->whatsapp_enabled && $settings->whatsapp_numbers) {
            $this->sendWhatsAppAlert(
                $settings->whatsapp_numbers,
                $message,
                $link->id,
                'link_down',
                $link->client->company_id
            );
        }
    }

    /**
     * Check and send high latency alert
     */
    public function checkHighLatencyAlert(ClientLink $link)
    {
        $settings = NotificationSetting::where('company_id', $link->client->company_id)->first();

        if (!$settings || !$settings->high_latency_enabled) {
            return;
        }

        $threshold = $settings->high_latency_threshold_ms ?? 50;
        $duration = $settings->high_latency_duration_minutes ?? 10;

        // Check if latency is consistently high
        $highLatencyCount = LinkMonitoringData::where('client_link_id', $link->id)
            ->where('collected_at', '>=', now()->subMinutes($duration))
            ->where('latency_ms', '>', $threshold)
            ->count();

        $totalSamples = LinkMonitoringData::where('client_link_id', $link->id)
            ->where('collected_at', '>=', now()->subMinutes($duration))
            ->count();

        if ($totalSamples < 3 || $highLatencyCount < ($totalSamples * 0.8)) {
            return; // Not sustained high latency
        }

        // Check cooldown
        if ($settings->isInCooldown($link->id, 'high_latency')) {
            return;
        }

        $avgLatency = LinkMonitoringData::where('client_link_id', $link->id)
            ->where('collected_at', '>=', now()->subMinutes($duration))
            ->avg('latency_ms');

        $recipients = array_merge(
            $settings->high_latency_recipients ?? [],
            $settings->getOperationsEmails()
        );
        $recipients = array_unique(array_filter($recipients));

        $message = sprintf(
            "⚠️ HIGH LATENCY ALERT\n\nLink: %s\nRouter: %s\nInterface: %s\nAverage Latency: %.2f ms (Threshold: %d ms)\nDuration: %d minutes\n\nInvestigation required!",
            $link->deliverable->deliverable_id ?? 'N/A',
            $link->router->name,
            $link->interface_name,
            $avgLatency,
            $threshold,
            $duration
        );

        // Send email
        if ($settings->email_enabled && !empty($recipients)) {
            $this->sendEmailNotification(
                $recipients,
                new HighLatencyAlert($link, $avgLatency, $threshold),
                $link->id,
                'high_latency',
                $link->client->company_id
            );
        }

        // Send WhatsApp
        if ($settings->whatsapp_enabled && $settings->whatsapp_numbers) {
            $this->sendWhatsAppAlert(
                $settings->whatsapp_numbers,
                $message,
                $link->id,
                'high_latency',
                $link->client->company_id
            );
        }
    }

    /**
     * Check and send high packet loss alert
     */
    public function checkHighPacketLossAlert(ClientLink $link)
    {
        $settings = NotificationSetting::where('company_id', $link->client->company_id)->first();

        if (!$settings || !$settings->high_packet_loss_enabled) {
            return;
        }

        $threshold = $settings->high_packet_loss_threshold_percent ?? 2.0;
        $duration = $settings->high_packet_loss_duration_minutes ?? 10;

        $avgPacketLoss = LinkMonitoringData::where('client_link_id', $link->id)
            ->where('collected_at', '>=', now()->subMinutes($duration))
            ->whereNotNull('packet_loss_percent')
            ->avg('packet_loss_percent');

        if ($avgPacketLoss <= $threshold) {
            return;
        }

        // Check cooldown
        if ($settings->isInCooldown($link->id, 'high_packet_loss')) {
            return;
        }

        $recipients = array_merge(
            $settings->high_packet_loss_recipients ?? [],
            $settings->getOperationsEmails()
        );
        $recipients = array_unique(array_filter($recipients));

        $message = sprintf(
            "⚠️ HIGH PACKET LOSS ALERT\n\nLink: %s\nRouter: %s\nInterface: %s\nAverage Packet Loss: %.2f%% (Threshold: %.2f%%)\nDuration: %d minutes\n\nInvestigation required!",
            $link->deliverable->deliverable_id ?? 'N/A',
            $link->router->name,
            $link->interface_name,
            $avgPacketLoss,
            $threshold,
            $duration
        );

        // Send email
        if ($settings->email_enabled && !empty($recipients)) {
            $this->sendEmailNotification(
                $recipients,
                new HighPacketLossAlert($link, $avgPacketLoss, $threshold),
                $link->id,
                'high_packet_loss',
                $link->client->company_id
            );
        }

        // Send WhatsApp
        if ($settings->whatsapp_enabled && $settings->whatsapp_numbers) {
            $this->sendWhatsAppAlert(
                $settings->whatsapp_numbers,
                $message,
                $link->id,
                'high_packet_loss',
                $link->client->company_id
            );
        }
    }

    /**
     * Send email notification and log
     */
    protected function sendEmailNotification($recipients, $mailable, $linkId, $type, $companyId)
    {
        try {
            foreach ($recipients as $recipient) {
                Mail::to($recipient)->queue($mailable);
            }

            NotificationLog::create([
                'client_link_id' => $linkId,
                'company_id' => $companyId,
                'notification_type' => $type,
                'channel' => 'email',
                'recipients' => $recipients,
                'message' => class_basename($mailable),
                'sent_successfully' => true,
                'sent_at' => now(),
            ]);

            Log::info("Email notification sent for {$type} to " . count($recipients) . " recipients");
        } catch (\Exception $e) {
            NotificationLog::create([
                'client_link_id' => $linkId,
                'company_id' => $companyId,
                'notification_type' => $type,
                'channel' => 'email',
                'recipients' => $recipients,
                'message' => class_basename($mailable),
                'sent_successfully' => false,
                'error_message' => $e->getMessage(),
            ]);

            Log::error("Failed to send email notification: " . $e->getMessage());
        }
    }

    /**
     * Send WhatsApp notification and log
     */
    protected function sendWhatsAppAlert($numbers, $message, $linkId, $type, $companyId)
    {
        try {
            foreach ($numbers as $number) {
                WhatsAppHelper::whatsappNotification($message, $number);
            }

            NotificationLog::create([
                'client_link_id' => $linkId,
                'company_id' => $companyId,
                'notification_type' => $type,
                'channel' => 'whatsapp',
                'recipients' => $numbers,
                'message' => $message,
                'sent_successfully' => true,
                'sent_at' => now(),
            ]);

            Log::info("WhatsApp notification sent for {$type} to " . count($numbers) . " numbers");
        } catch (\Exception $e) {
            NotificationLog::create([
                'client_link_id' => $linkId,
                'company_id' => $companyId,
                'notification_type' => $type,
                'channel' => 'whatsapp',
                'recipients' => $numbers,
                'message' => $message,
                'sent_successfully' => false,
                'error_message' => $e->getMessage(),
            ]);

            Log::error("Failed to send WhatsApp notification: " . $e->getMessage());
        }
    }

    /**
     * Format SLA breach for WhatsApp
     */
    protected function formatSlaBreachWhatsApp($slaReport)
    {
        $link = $slaReport->clientLink;
        
        return sprintf(
            "⚠️ SLA BREACH ALERT\n\n" .
            "Month: %s\n" .
            "Link: %s\n" .
            "Router: %s\n" .
            "Interface: %s\n\n" .
            "Uptime: %.2f%% (Required: %.2f%%)\n" .
            "Avg Latency: %.2f ms (Max: %.2f ms)\n" .
            "Avg Packet Loss: %.2f%% (Max: %.2f%%)\n\n" .
            "Please review the detailed SLA report.",
            \Carbon\Carbon::parse($slaReport->report_month)->format('F Y'),
            $link->deliverable->deliverable_id ?? 'N/A',
            $link->router->name,
            $link->interface_name,
            $slaReport->uptime_percentage,
            $link->sla_uptime,
            $slaReport->avg_latency_ms,
            $link->sla_latency,
            $slaReport->avg_packet_loss_percent,
            $link->sla_packet_loss
        );
    }
}
