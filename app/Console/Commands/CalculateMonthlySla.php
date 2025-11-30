<?php

namespace App\Console\Commands;

use App\Models\ClientLink;
use App\Models\LinkMonitoringData;
use App\Models\SlaReport;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalculateMonthlySla extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sla:calculate-monthly {--month= : Month to calculate (YYYY-MM format)} {--link= : Specific link ID to calculate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate monthly SLA reports for client links based on monitoring data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $month = $this->option('month') ?? Carbon::now()->subMonth()->format('Y-m');
        $linkId = $this->option('link');

        $this->info("Calculating SLA reports for {$month}...");

        // Get links to process
        $query = ClientLink::where('status', 'active');
        
        if ($linkId) {
            $query->where('id', $linkId);
        }
        
        $links = $query->get();

        if ($links->isEmpty()) {
            $this->warn('No active links found.');
            return 0;
        }

        $startDate = Carbon::parse($month . '-01')->startOfMonth();
        $endDate = Carbon::parse($month . '-01')->endOfMonth();

        $this->info("Processing {$links->count()} links for period: {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}");

        $bar = $this->output->createProgressBar($links->count());
        $bar->start();

        foreach ($links as $link) {
            try {
                $this->calculateLinkSla($link, $startDate, $endDate, $month);
                $bar->advance();
            } catch (\Exception $e) {
                Log::error("SLA calculation failed for link {$link->id}: " . $e->getMessage());
                $this->error("\nError calculating SLA for link {$link->id}: " . $e->getMessage());
            }
        }

        $bar->finish();
        $this->info("\n✓ SLA calculation completed!");

        return 0;
    }

    /**
     * Calculate SLA for a specific link
     */
    protected function calculateLinkSla(ClientLink $link, Carbon $startDate, Carbon $endDate, string $month)
    {
        // Get all monitoring data for the month
        $monitoringData = LinkMonitoringData::where('client_link_id', $link->id)
            ->whereBetween('collected_at', [$startDate, $endDate])
            ->orderBy('collected_at', 'asc')
            ->get();

        if ($monitoringData->isEmpty()) {
            Log::warning("No monitoring data found for link {$link->id} in {$month}");
            return;
        }

        // Calculate uptime percentage
        $totalSeconds = $endDate->diffInSeconds($startDate);
        $uptimeSeconds = $monitoringData->where('interface_status', 'up')->count() * 300; // 5 minutes per sample
        $downtimeSeconds = $totalSeconds - $uptimeSeconds;
        $uptimePercentage = ($uptimeSeconds / $totalSeconds) * 100;

        // Calculate average latency
        $avgLatency = $monitoringData->whereNotNull('latency_ms')->avg('latency_ms') ?? 0;

        // Calculate average packet loss
        $avgPacketLoss = $monitoringData->whereNotNull('packet_loss_percent')->avg('packet_loss_percent') ?? 0;

        // Determine if SLA was met
        $slaMet = $uptimePercentage >= $link->sla_uptime &&
                  $avgLatency <= $link->sla_latency &&
                  $avgPacketLoss <= $link->sla_packet_loss;

        // Build breach details
        $breachDetails = [];
        
        if ($uptimePercentage < $link->sla_uptime) {
            $breachDetails[] = sprintf(
                'Uptime: %.2f%% (Required: %.2f%%) - Breach of %.2f%%',
                $uptimePercentage,
                $link->sla_uptime,
                $link->sla_uptime - $uptimePercentage
            );
        }

        if ($avgLatency > $link->sla_latency) {
            $breachDetails[] = sprintf(
                'Average Latency: %.2fms (Required: %.2fms) - Exceeded by %.2fms',
                $avgLatency,
                $link->sla_latency,
                $avgLatency - $link->sla_latency
            );
        }

        if ($avgPacketLoss > $link->sla_packet_loss) {
            $breachDetails[] = sprintf(
                'Average Packet Loss: %.2f%% (Required: %.2f%%) - Exceeded by %.2f%%',
                $avgPacketLoss,
                $link->sla_packet_loss,
                $avgPacketLoss - $link->sla_packet_loss
            );
        }

        // Find downtime incidents (consecutive down periods)
        $incidents = $this->findDowntimeIncidents($monitoringData);

        if (!empty($incidents)) {
            $breachDetails[] = sprintf('Total Downtime Incidents: %d', count($incidents));
            foreach ($incidents as $i => $incident) {
                $breachDetails[] = sprintf(
                    'Incident #%d: %s to %s (%s)',
                    $i + 1,
                    $incident['start']->format('Y-m-d H:i'),
                    $incident['end']->format('Y-m-d H:i'),
                    $this->formatDuration($incident['duration'])
                );
            }
        }

        // Create or update SLA report
        $report = SlaReport::updateOrCreate(
            [
                'client_link_id' => $link->id,
                'report_month' => $month . '-01',
            ],
            [
                'uptime_percentage' => round($uptimePercentage, 4),
                'total_downtime_seconds' => $downtimeSeconds,
                'avg_latency_ms' => round($avgLatency, 2),
                'avg_packet_loss_percent' => round($avgPacketLoss, 2),
                'sla_met' => $slaMet,
                'breach_details' => !empty($breachDetails) ? $breachDetails : null,
                'calculated_at' => now(),
            ]
        );

        Log::info("SLA report created for link {$link->id} ({$month}): " . ($slaMet ? 'Met' : 'Breach'));

        // Send notification if SLA was breached (via NotificationService)
        if (!$slaMet) {
            $notificationService = new NotificationService();
            $notificationService->sendSlaBreachNotification($report);
        }
    }

    /**
     * Find downtime incidents from monitoring data
     */
    protected function findDowntimeIncidents($monitoringData)
    {
        $incidents = [];
        $currentIncident = null;

        foreach ($monitoringData as $data) {
            if ($data->interface_status === 'down') {
                if (!$currentIncident) {
                    $currentIncident = [
                        'start' => $data->collected_at,
                        'end' => $data->collected_at,
                    ];
                } else {
                    $currentIncident['end'] = $data->collected_at;
                }
            } else {
                if ($currentIncident) {
                    $currentIncident['duration'] = $currentIncident['end']->diffInSeconds($currentIncident['start']);
                    $incidents[] = $currentIncident;
                    $currentIncident = null;
                }
            }
        }

        // Close any ongoing incident
        if ($currentIncident) {
            $currentIncident['duration'] = $currentIncident['end']->diffInSeconds($currentIncident['start']);
            $incidents[] = $currentIncident;
        }

        return $incidents;
    }

    /**
     * Format duration in human-readable format
     */
    protected function formatDuration($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;

        $parts = [];
        if ($hours > 0) $parts[] = "{$hours}h";
        if ($minutes > 0) $parts[] = "{$minutes}m";
        if ($seconds > 0 || empty($parts)) $parts[] = "{$seconds}s";

        return implode(' ', $parts);
    }
}
