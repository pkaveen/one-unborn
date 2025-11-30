<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ClientLink;
use App\Models\LinkMonitoringData;
use App\Services\MikrotikService;
use Illuminate\Support\Facades\Log;

class CollectLinkMetrics implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;

    protected $linkId;

    public function __construct($linkId = null)
    {
        $this->linkId = $linkId;
    }

    public function handle(MikrotikService $mikrotikService): void
    {
        $query = ClientLink::with('router')->active();

        if ($this->linkId) {
            $query->where('id', $this->linkId);
        }

        $links = $query->get();

        foreach ($links as $link) {
            try {
                $this->collectMetricsForLink($link, $mikrotikService);
            } catch (\Exception $e) {
                Log::error("Failed to collect metrics for link {$link->id}", [
                    'link_id' => $link->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    protected function collectMetricsForLink(ClientLink $link, MikrotikService $mikrotikService): void
    {
        $router = $link->router;

        if (!$router || !$router->is_active) {
            Log::warning("Router not active for link {$link->id}");
            return;
        }

        $client = $mikrotikService->connect($router);

        if (!$client) {
            Log::error("Failed to connect to router for link {$link->id}");
            return;
        }

        // Get interface counters
        $counters = $mikrotikService->getInterfaceCounters($client, $link->interface_name);
        
        // Get current traffic rates
        $traffic = $mikrotikService->getInterfaceTraffic($client, $link->interface_name);
        
        // Check if interface is up
        $isUp = $mikrotikService->isInterfaceUp($client, $link->interface_name);

        // Get ping stats (ping default gateway or configured target)
        $pingTarget = $link->monitoring_config['ping_target'] ?? '8.8.8.8';
        $pingStats = $mikrotikService->pingHost($client, $pingTarget, 5);

        // Store monitoring data
        LinkMonitoringData::create([
            'link_id' => $link->id,
            'timestamp' => now(),
            'rx_bytes' => $counters['rx_bytes'] ?? 0,
            'tx_bytes' => $counters['tx_bytes'] ?? 0,
            'rx_packets' => $counters['rx_packets'] ?? 0,
            'tx_packets' => $counters['tx_packets'] ?? 0,
            'rx_errors' => $counters['rx_errors'] ?? 0,
            'tx_errors' => $counters['tx_errors'] ?? 0,
            'rx_rate_mbps' => $traffic['rx_rate_mbps'] ?? 0,
            'tx_rate_mbps' => $traffic['tx_rate_mbps'] ?? 0,
            'latency_ms' => $pingStats['avg_latency_ms'],
            'packet_loss_percent' => $pingStats['packet_loss_percent'],
            'link_status' => $isUp ? 'up' : 'down',
        ]);

        Log::info("Collected metrics for link {$link->id}", [
            'link_name' => $link->link_name,
            'status' => $isUp ? 'up' : 'down',
            'rx_rate' => $traffic['rx_rate_mbps'] ?? 0,
            'tx_rate' => $traffic['tx_rate_mbps'] ?? 0,
        ]);
    }
}
