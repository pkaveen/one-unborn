@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-hourglass-split me-2"></i>Open Deliverables</h5>
                    <span class="badge bg-light text-dark">{{ $records->count() }} Records</span>
                </div>

                <div class="card-body">
                    @if($records->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Delivery ID</th>
                                        <th>Feasibility</th>
                                        <th>Client</th>
                                        <th>PO Number</th>
                                        <th>PO Date</th>
                                        <th>Site Address</th>
                                        <th>Link Type</th>
                                        <th>Speed</th>
                                        <th>Vendor</th>
                                        <th>Total Cost</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($records as $record)
                                        <tr>
                                            <td>
                                                <span class="badge bg-info">{{ $record->delivery_id }}</span>
                                            </td>
                                            <td>
                                                @if($record->feasibility)
                                                    <small class="text-muted">{{ $record->feasibility->feasibility_request_id }}</small>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($record->feasibility && $record->feasibility->client)
                                                    <strong>{{ $record->feasibility->client->client_name }}</strong>
                                                @else
                                                    <span class="text-muted">No Client</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($record->purchaseOrder)
                                                    <span class="badge bg-success">{{ $record->purchaseOrder->po_number }}</span>
                                                @else
                                                    <span class="text-muted">No PO</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($record->purchaseOrder)
                                                    <small class="text-muted">{{ $record->purchaseOrder->po_date->format('d-M-Y') }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $record->site_address ?? 'Not specified' }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $record->link_type ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                {{ $record->speed_in_mbps ?? 'N/A' }} Mbps
                                            </td>
                                            <td>
                                                {{ $record->vendor ?? 'Not assigned' }}
                                            </td>
                                            <td>
                                                @if($record->purchaseOrder)
                                                    @php
                                                        $totalCost = ($record->purchaseOrder->arc_per_link + $record->purchaseOrder->otc_per_link + $record->purchaseOrder->static_ip_cost_per_link) * $record->purchaseOrder->no_of_links;
                                                    @endphp
                                                    <strong class="text-success">₹{{ number_format($totalCost, 2) }}</strong>
                                                    <br><small class="text-muted">{{ $record->purchaseOrder->no_of_links }} Links</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-hourglass-split"></i> {{ $record->status }}
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $record->created_at->format('M d, Y') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('operations.deliverables.view', $record->id) }}" 
                                                       class="btn btn-outline-info btn-sm"
                                                       title="View Details">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('operations.deliverables.edit', $record->id) }}" 
                                                       class="btn btn-outline-primary btn-sm"
                                                       title="Edit Deliverable">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="bi bi-inbox display-1 text-muted"></i>
                            </div>
                            <h5 class="text-muted">No Open Deliverables Found</h5>
                            <p class="text-muted">There are currently no deliverables in "Open" status.</p>
                            <p class="small text-muted">
                                New deliverables are automatically created when:<br>
                                • <strong>Purchase Orders are created</strong> for closed feasibilities<br>
                                • Each deliverable includes complete PO details, costs, and client information<br>
                                • Only feasibilities with approved purchase orders will have deliverables
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
        <div class="toast show bg-success text-white" role="alert">
            <div class="toast-header bg-success text-white">
                <i class="bi bi-check-circle-fill me-2"></i>
                <strong class="me-auto">Success</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                {{ session('success') }}
            </div>
        </div>
    </div>
@endif

@endsection