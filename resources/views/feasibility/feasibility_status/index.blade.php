@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-primary">Feasibility Status - {{ ucfirst($status) }}</h4>
    </div>

    {{-- 🔖 Tabs for Status --}}
    <ul class="nav nav-tabs mb-3">
        @foreach($statuses as $tab)
            <li class="nav-item">
                <a class="nav-link {{ $tab == $status ? 'active' : '' }}"
                   href="{{ route('feasibility.status.index', $tab) }}">
                   {{ $tab }}
                </a>
            </li>
        @endforeach
    </ul>

    {{-- 🧩 Table --}}
    <div class="card shadow-sm border-0">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-primary text-center">
                    <tr>
                        <th rowspan="2">S.No</th>
                        <th rowspan="2">Client</th>
                        <th colspan="5">Vendor 1</th>
                        <th colspan="5">Vendor 2</th>
                        <th colspan="5">Vendor 3</th>
                        <th colspan="5">Vendor 4</th>
                        <th rowspan="2">Status</th>
                        <th rowspan="2">Action</th>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <th>ARC</th>
                        <th>OTC</th>
                        <th>Static IP Cost</th>
                        <th>Delivery Timeline</th>

                        <th>Name</th>
                        <th>ARC</th>
                        <th>OTC</th>
                        <th>Static IP Cost</th>
                        <th>Delivery Timeline</th>

                        <th>Name</th>
                        <th>ARC</th>
                        <th>OTC</th>
                        <th>Static IP Cost</th>
                        <th>Delivery Timeline</th>

                        <th>Name</th>
                        <th>ARC</th>
                        <th>OTC</th>
                        <th>Static IP Cost</th>
                        <th>Delivery Timeline</th>
                    </tr>
                </thead>

                <tbody class="text-center">
                    @forelse($records as $key => $record)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $record->feasibility->client->client_name ?? 'N/A' }}</td>

                            {{-- Vendor 1 --}}
                            <td>{{ $record->vendor1_name ?? '-' }}</td>
                            <td>{{ $record->vendor1_arc ?? '-' }}</td>
                            <td>{{ $record->vendor1_otc ?? '-' }}</td>
                            <td>{{ $record->vendor1_static_ip_cost ?? '-' }}</td>
                            <td>{{ $record->vendor1_delivery_timeline ?? '-' }}</td>

                            {{-- Vendor 2 --}}
                            <td>{{ $record->vendor2_name ?? '-' }}</td>
                            <td>{{ $record->vendor2_arc ?? '-' }}</td>
                            <td>{{ $record->vendor2_otc ?? '-' }}</td>
                            <td>{{ $record->vendor2_static_ip_cost ?? '-' }}</td>
                            <td>{{ $record->vendor2_delivery_timeline ?? '-' }}</td>

                            {{-- Vendor 3 --}}
                            <td>{{ $record->vendor3_name ?? '-' }}</td>
                            <td>{{ $record->vendor3_arc ?? '-' }}</td>
                            <td>{{ $record->vendor3_otc ?? '-' }}</td>
                            <td>{{ $record->vendor3_static_ip_cost ?? '-' }}</td>
                            <td>{{ $record->vendor3_delivery_timeline ?? '-' }}</td>

                            {{-- Vendor 4 --}}
                            <td>{{ $record->vendor4_name ?? '-' }}</td>
                            <td>{{ $record->vendor4_arc ?? '-' }}</td>
                            <td>{{ $record->vendor4_otc ?? '-' }}</td>
                            <td>{{ $record->vendor4_static_ip_cost ?? '-' }}</td>
                            <td>{{ $record->vendor4_delivery_timeline ?? '-' }}</td>

                            {{-- Status --}}
                            <td>
                                <span class="badge bg-{{ $record->status == 'Closed' ? 'success' : ($record->status == 'InProgress' ? 'warning' : 'secondary') }}">
                                    {{ $record->status }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td>
                                <a href="{{ route('feasibility.status.show', $record->id) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i> 
                                </a>
                                <a href="{{ route('feasibility.status.edit', $record->id) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil-square"></i> 
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="28" class="text-center text-muted">No records found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
