@extends('client_portal.layout')

@section('title', 'Link Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('client.links') }}" class="text-blue-600 hover:text-blue-800">
            ← Back to Links
        </a>
    </div>

    <!-- Link Information -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h1 class="text-2xl font-bold mb-6">{{ $link->deliverable->deliverable_id ?? 'Link Details' }}</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-gray-600 text-sm">Router</p>
                <p class="font-semibold">{{ $link->router->name }}</p>
                <p class="text-sm text-gray-500">{{ $link->router->management_ip }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Interface</p>
                <p class="font-semibold">{{ $link->interface_name }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Link Type</p>
                <p class="font-semibold">{{ $link->link_type }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Committed Bandwidth</p>
                <p class="font-semibold">{{ $link->bandwidth_committed }} Mbps</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">SLA Uptime Commitment</p>
                <p class="font-semibold">{{ $link->sla_uptime }}%</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Max Latency</p>
                <p class="font-semibold">{{ $link->sla_latency }} ms</p>
            </div>
        </div>
    </div>

    <!-- Traffic Graph -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Traffic Statistics (Last 24 Hours)</h2>
        <canvas id="trafficChart" height="80"></canvas>
    </div>

    <!-- Latency Graph -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Latency (Last 24 Hours)</h2>
        <canvas id="latencyChart" height="80"></canvas>
    </div>

    <!-- Grafana Embed (if available) -->
    @if(config('services.grafana.enabled', false))
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">Real-time Dashboard</h2>
        <iframe 
            src="{{ config('services.grafana.url') }}/d/client-link/client-link-dashboard?var-link_id={{ $link->id }}&theme=light&kiosk" 
            width="100%" 
            height="600" 
            frameborder="0"
            class="rounded-lg"
        ></iframe>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    const monitoringData = @json($monitoringData);
    
    // Traffic Chart
    const trafficCtx = document.getElementById('trafficChart').getContext('2d');
    new Chart(trafficCtx, {
        type: 'line',
        data: {
            labels: monitoringData.map(d => new Date(d.collected_at).toLocaleTimeString()),
            datasets: [
                {
                    label: 'RX Traffic (MB)',
                    data: monitoringData.map(d => (d.rx_bytes / 1024 / 1024).toFixed(2)),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'TX Traffic (MB)',
                    data: monitoringData.map(d => (d.tx_bytes / 1024 / 1024).toFixed(2)),
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Traffic (MB)'
                    }
                }
            }
        }
    });

    // Latency Chart
    const latencyCtx = document.getElementById('latencyChart').getContext('2d');
    new Chart(latencyCtx, {
        type: 'line',
        data: {
            labels: monitoringData.map(d => new Date(d.collected_at).toLocaleTimeString()),
            datasets: [
                {
                    label: 'Latency (ms)',
                    data: monitoringData.map(d => d.latency_ms || 0),
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Latency (ms)'
                    }
                }
            }
        }
    });
</script>
@endpush
