@extends('client_portal.layout')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Welcome, {{ Auth::guard('client')->user()->username }}</h1>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Links</p>
                    <p class="text-2xl font-bold">{{ $links->count() }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Active Links</p>
                    <p class="text-2xl font-bold text-green-600">{{ $links->where('is_up', true)->count() }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Down Links</p>
                    <p class="text-2xl font-bold text-red-600">{{ $links->where('is_up', false)->count() }}</p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Avg SLA</p>
                    <p class="text-2xl font-bold">{{ number_format($slaReports->avg('uptime_percentage') ?? 0, 2) }}%</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Links Status -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b">
            <h2 class="text-xl font-semibold">Your Links</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($links as $link)
                    <div class="border rounded-lg p-4 hover:shadow-lg transition">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="font-semibold text-lg">{{ $link->deliverable->deliverable_id ?? 'N/A' }}</h3>
                                <p class="text-sm text-gray-600">{{ $link->router->name }} - {{ $link->interface_name }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $link->is_up ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $link->is_up ? 'Active' : 'Down' }}
                            </span>
                        </div>
                        
                        @if($link->latest_data)
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-600">RX Traffic</p>
                                    <p class="font-semibold">{{ number_format($link->latest_data->rx_bytes / 1024 / 1024, 2) }} MB</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">TX Traffic</p>
                                    <p class="font-semibold">{{ number_format($link->latest_data->tx_bytes / 1024 / 1024, 2) }} MB</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Latency</p>
                                    <p class="font-semibold">{{ number_format($link->latest_data->latency_ms ?? 0, 2) }} ms</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Packet Loss</p>
                                    <p class="font-semibold">{{ number_format($link->latest_data->packet_loss_percent ?? 0, 2) }}%</p>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-3">Last updated: {{ $link->latest_data->collected_at->diffForHumans() }}</p>
                        @else
                            <p class="text-sm text-gray-500">No monitoring data available</p>
                        @endif

                        <a href="{{ route('client.links.details', $link->id) }}" class="block mt-4 text-blue-600 hover:text-blue-800 text-sm font-semibold">
                            View Details →
                        </a>
                    </div>
                @empty
                    <p class="text-gray-600 col-span-2">No links configured yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent SLA Reports -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h2 class="text-xl font-semibold">Recent SLA Reports</h2>
            <a href="{{ route('client.sla.reports') }}" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                View All →
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Month</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Link</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Uptime</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avg Latency</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SLA Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($slaReports as $report)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($report->report_month)->format('M Y') }}</td>
                            <td class="px-6 py-4">{{ $report->clientLink->deliverable->deliverable_id ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ number_format($report->uptime_percentage, 2) }}%</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ number_format($report->avg_latency_ms, 2) }} ms</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $report->sla_met ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $report->sla_met ? 'Met' : 'Breach' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No SLA reports available yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
