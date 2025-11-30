@extends('client_portal.layout')

@section('title', 'My Links')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">My Links</h1>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Link ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Router</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Interface</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bandwidth</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SLA Commitment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($links as $link)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap font-medium">
                                {{ $link->deliverable->deliverable_id ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4">{{ $link->router->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $link->interface_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $link->link_type }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $link->bandwidth_committed }} Mbps</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $link->sla_uptime }}%</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($link->latestMonitoringData && $link->latestMonitoringData->interface_status === 'up')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        Down
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('client.links.details', $link->id) }}" 
                                   class="text-blue-600 hover:text-blue-800 font-semibold">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                No links configured yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
