<?php

namespace App\Services;

use RouterOS\Client;
use RouterOS\Config;
use RouterOS\Query;
use Illuminate\Support\Facades\Log;
use App\Models\MikrotikRouter;

class MikrotikService
{
    /**
     * Create RouterOS API client connection
     */
    public function connect(MikrotikRouter $router): ?Client
    {
        try {
            $config = (new Config())
                ->set('host', $router->management_ip)
                ->set('user', $router->api_username)
                ->set('pass', $router->api_password)
                ->set('port', $router->api_port);

            if ($router->use_ssl) {
                $config->set('ssl', true);
            }

            $client = new Client($config);
            
            // Update router status
            $router->update([
                'status' => 'online',
                'last_poll' => now(),
            ]);

            return $client;
        } catch (\Exception $e) {
            Log::error("MikroTik connection failed: {$router->management_ip}", [
                'error' => $e->getMessage(),
                'router_id' => $router->id,
            ]);

            $router->update(['status' => 'unreachable']);
            return null;
        }
    }

    /**
     * Get interface statistics
     */
    public function getInterfaceStats(Client $client, string $interfaceName): ?array
    {
        try {
            $query = (new Query('/interface/print'))
                ->where('name', $interfaceName);

            $response = $client->query($query)->read();
            return $response[0] ?? null;
        } catch (\Exception $e) {
            Log::error("Failed to get interface stats", [
                'interface' => $interfaceName,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Get real-time interface traffic (RX/TX rates)
     */
    public function getInterfaceTraffic(Client $client, string $interfaceName): ?array
    {
        try {
            $query = (new Query('/interface/monitor-traffic'))
                ->equal('interface', $interfaceName)
                ->equal('once', '');

            $response = $client->query($query)->read();
            $data = $response[0] ?? null;

            if ($data) {
                return [
                    'rx_rate_mbps' => isset($data['rx-bits-per-second']) ? 
                        round($data['rx-bits-per-second'] / 1000000, 2) : 0,
                    'tx_rate_mbps' => isset($data['tx-bits-per-second']) ? 
                        round($data['tx-bits-per-second'] / 1000000, 2) : 0,
                    'rx_packets_per_second' => $data['rx-packets-per-second'] ?? 0,
                    'tx_packets_per_second' => $data['tx-packets-per-second'] ?? 0,
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error("Failed to get interface traffic", [
                'interface' => $interfaceName,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Get cumulative interface counters
     */
    public function getInterfaceCounters(Client $client, string $interfaceName): ?array
    {
        try {
            $query = (new Query('/interface/print'))
                ->equal('stats', '')
                ->where('name', $interfaceName);

            $response = $client->query($query)->read();
            $data = $response[0] ?? null;

            if ($data) {
                return [
                    'rx_bytes' => $data['rx-byte'] ?? 0,
                    'tx_bytes' => $data['tx-byte'] ?? 0,
                    'rx_packets' => $data['rx-packet'] ?? 0,
                    'tx_packets' => $data['tx-packet'] ?? 0,
                    'rx_errors' => $data['rx-error'] ?? 0,
                    'tx_errors' => $data['tx-error'] ?? 0,
                    'rx_drops' => $data['rx-drop'] ?? 0,
                    'tx_drops' => $data['tx-drop'] ?? 0,
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error("Failed to get interface counters", [
                'interface' => $interfaceName,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Ping a host to check latency and packet loss
     */
    public function pingHost(Client $client, string $address, int $count = 5): array
    {
        try {
            $query = (new Query('/ping'))
                ->equal('address', $address)
                ->equal('count', (string)$count);

            $response = $client->query($query)->read();

            $totalRtt = 0;
            $received = 0;
            $minRtt = PHP_INT_MAX;
            $maxRtt = 0;

            foreach ($response as $ping) {
                if (isset($ping['time']) && $ping['time'] !== 'timeout') {
                    $rtt = (int) str_replace('ms', '', $ping['time']);
                    $totalRtt += $rtt;
                    $received++;
                    $minRtt = min($minRtt, $rtt);
                    $maxRtt = max($maxRtt, $rtt);
                }
            }

            return [
                'sent' => $count,
                'received' => $received,
                'packet_loss_percent' => round((($count - $received) / $count) * 100, 2),
                'avg_latency_ms' => $received > 0 ? round($totalRtt / $received, 2) : null,
                'min_latency_ms' => $received > 0 ? $minRtt : null,
                'max_latency_ms' => $received > 0 ? $maxRtt : null,
            ];
        } catch (\Exception $e) {
            Log::error("Failed to ping host", [
                'address' => $address,
                'error' => $e->getMessage(),
            ]);
            return [
                'sent' => $count,
                'received' => 0,
                'packet_loss_percent' => 100,
                'avg_latency_ms' => null,
                'min_latency_ms' => null,
                'max_latency_ms' => null,
            ];
        }
    }

    /**
     * Get system resources (CPU, memory, uptime)
     */
    public function getSystemResources(Client $client): ?array
    {
        try {
            $query = new Query('/system/resource/print');
            $response = $client->query($query)->read();
            $data = $response[0] ?? null;

            if ($data) {
                return [
                    'uptime' => $data['uptime'] ?? null,
                    'cpu_load' => $data['cpu-load'] ?? 0,
                    'free_memory' => $data['free-memory'] ?? 0,
                    'total_memory' => $data['total-memory'] ?? 0,
                    'free_hdd' => $data['free-hdd-space'] ?? 0,
                    'total_hdd' => $data['total-hdd-space'] ?? 0,
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error("Failed to get system resources", [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Check if interface is up
     */
    public function isInterfaceUp(Client $client, string $interfaceName): bool
    {
        try {
            $stats = $this->getInterfaceStats($client, $interfaceName);
            return isset($stats['running']) && $stats['running'] === 'true';
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get all interfaces
     */
    public function getAllInterfaces(Client $client): array
    {
        try {
            $query = new Query('/interface/print');
            return $client->query($query)->read();
        } catch (\Exception $e) {
            Log::error("Failed to get all interfaces", [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Test connection to router
     */
    public function testConnection(MikrotikRouter $router): bool
    {
        $client = $this->connect($router);
        
        if (!$client) {
            return false;
        }

        try {
            $query = new Query('/system/identity/print');
            $client->query($query)->read();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
