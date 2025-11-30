@extends('client_portal.layout')

@section('title', 'SLA Reports')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">SLA Reports</h1>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form method="GET" action="{{ route('client.sla.reports') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Month</label>
                <input 
                    type="month" 
                    name="month" 
                    value="{{ request('month') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Link</label>
                <select 
                    name="link_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                >
                    <option value="">All Links</option>
                    @foreach($links as $link)
                        <option value="{{ $link->id }}" {{ request('link_id') == $link->id ? 'selected' : '' }}>
                            {{ $link->deliverable->deliverable_id ?? 'Link #' . $link->id }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Reports Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Month</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Link</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Uptime %</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Downtime</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avg Latency</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avg Packet Loss</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SLA Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($reports as $report)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap font-medium">
                                {{ \Carbon\Carbon::parse($report->report_month)->format('F Y') }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $report->clientLink->deliverable->deliverable_id ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="font-semibold {{ $report->uptime_percentage >= $report->clientLink->sla_uptime ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($report->uptime_percentage, 2) }}%
                                    </span>
                                    <span class="ml-2 text-xs text-gray-500">
                                        ({{ number_format($report->clientLink->sla_uptime, 2) }}% committed)
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ gmdate('H:i:s', $report->total_downtime_seconds) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ number_format($report->avg_latency_ms, 2) }} ms
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ number_format($report->avg_packet_loss_percent, 2) }}%
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($report->sla_met)
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        ✓ Met
                                    </span>
                                @else
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        ✗ Breach
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('client.sla.download', $report->id) }}" 
                                   class="text-blue-600 hover:text-blue-800 font-semibold">
                                    Download PDF
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                No SLA reports found for the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($reports->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $reports->links() }}
            </div>
        @endif
    </div>

    <!-- SLA Breach Details -->
    @if($reports->where('sla_met', false)->count() > 0)
        <div class="bg-red-50 border border-red-200 rounded-lg p-6 mt-8">
            <h2 class="text-xl font-semibold text-red-800 mb-4">⚠️ SLA Breach Details</h2>
            @foreach($reports->where('sla_met', false) as $breach)
                <div class="bg-white rounded p-4 mb-4">
                    <p class="font-semibold">{{ $breach->clientLink->deliverable->deliverable_id ?? 'Link' }} - {{ \Carbon\Carbon::parse($breach->report_month)->format('F Y') }}</p>
                    @if($breach->breach_details)
                        <ul class="mt-2 text-sm text-gray-700 list-disc list-inside">
                            @foreach($breach->breach_details as $detail)
                                <li>{{ $detail }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
