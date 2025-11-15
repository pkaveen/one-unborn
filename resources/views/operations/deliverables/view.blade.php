@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow border-0">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-eye me-2"></i>Deliverable Details - {{ $record->delivery_id }}
                    </h5>
                    <div class="d-flex gap-2">
                        <span class="badge bg-light text-dark">{{ $record->status }}</span>
                        <a href="{{ route('operations.deliverables.edit', $record->id) }}" class="btn btn-light btn-sm">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <a href="{{ route('operations.deliverables.open') }}" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Left Column - Basic Information -->
                        <div class="col-lg-6">
                            <h6 class="text-primary mb-3"><i class="bi bi-info-circle"></i> Basic Information</h6>
                            
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Delivery ID:</th>
                                    <td><strong class="text-primary">{{ $record->delivery_id }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge 
                                            @if($record->status == 'Open') bg-warning text-dark
                                            @elseif($record->status == 'InProgress') bg-primary
                                            @elseif($record->status == 'Delivery') bg-success
                                            @else bg-secondary @endif">
                                            {{ $record->status }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created Date:</th>
                                    <td>{{ $record->created_at->format('d-M-Y H:i') }}</td>
                                </tr>
                            </table>

                            <h6 class="text-primary mb-3 mt-4"><i class="bi bi-building"></i> Client & Site Information</h6>
                            
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Client:</th>
                                    <td>
                                        @if($record->feasibility && $record->feasibility->client)
                                            <strong>{{ $record->feasibility->client->client_name }}</strong>
                                        @else
                                            <span class="text-muted">No Client</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Feasibility ID:</th>
                                    <td>
                                        @if($record->feasibility)
                                            <span class="badge bg-info">{{ $record->feasibility->feasibility_request_id }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Site Address:</th>
                                    <td>{{ $record->site_address ?? 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <th>Local Contact:</th>
                                    <td>{{ $record->local_contact ?? 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <th>State:</th>
                                    <td>{{ $record->state ?? 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <th>GST Number:</th>
                                    <td>{{ $record->gst_number ?? 'Not specified' }}</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Right Column - Purchase Order Information -->
                        <div class="col-lg-6">
                            @if($record->purchaseOrder)
                            <h6 class="text-success mb-3"><i class="bi bi-receipt"></i> Purchase Order Details</h6>
                            
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">PO Number:</th>
                                    <td><strong class="text-success">{{ $record->purchaseOrder->po_number }}</strong></td>
                                </tr>
                                <tr>
                                    <th>PO Date:</th>
                                    <td>{{ $record->purchaseOrder->po_date->format('d-M-Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Contract Period:</th>
                                    <td>{{ $record->purchaseOrder->contract_period }} Months</td>
                                </tr>
                                <tr>
                                    <th>Number of Links:</th>
                                    <td><span class="badge bg-primary">{{ $record->purchaseOrder->no_of_links }} Links</span></td>
                                </tr>
                            </table>

                            <h6 class="text-success mb-3 mt-4"><i class="bi bi-calculator"></i> Cost Breakdown</h6>
                            
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">ARC per Link:</th>
                                    <td class="text-end">₹{{ number_format($record->purchaseOrder->arc_per_link, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>OTC per Link:</th>
                                    <td class="text-end">₹{{ number_format($record->purchaseOrder->otc_per_link, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Static IP per Link:</th>
                                    <td class="text-end">₹{{ number_format($record->purchaseOrder->static_ip_cost_per_link, 2) }}</td>
                                </tr>
                                <tr class="table-success">
                                    <th>Total per Link:</th>
                                    <td class="text-end">
                                        <strong>₹{{ number_format($record->purchaseOrder->arc_per_link + $record->purchaseOrder->otc_per_link + $record->purchaseOrder->static_ip_cost_per_link, 2) }}</strong>
                                    </td>
                                </tr>
                                <tr class="table-primary">
                                    <th>Grand Total ({{ $record->purchaseOrder->no_of_links }} Links):</th>
                                    <td class="text-end">
                                        @php
                                            $grandTotal = ($record->purchaseOrder->arc_per_link + $record->purchaseOrder->otc_per_link + $record->purchaseOrder->static_ip_cost_per_link) * $record->purchaseOrder->no_of_links;
                                        @endphp
                                        <strong class="text-primary fs-5">₹{{ number_format($grandTotal, 2) }}</strong>
                                    </td>
                                </tr>
                            </table>
                            @else
                            <h6 class="text-muted mb-3"><i class="bi bi-exclamation-triangle"></i> Purchase Order Information</h6>
                            <div class="alert alert-warning">
                                <i class="bi bi-info-circle"></i> No purchase order linked to this deliverable.
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Technical Information -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3"><i class="bi bi-gear"></i> Technical Specifications</h6>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Link Type:</th>
                                            <td>{{ $record->link_type ?? 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Speed (Mbps):</th>
                                            <td>{{ $record->speed_in_mbps ?? 'Not specified' }} Mbps</td>
                                        </tr>
                                        <tr>
                                            <th>Vendor:</th>
                                            <td>{{ $record->vendor ?? 'Not assigned' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Circuit ID:</th>
                                            <td>{{ $record->circuit_id ?? 'Not assigned' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Plans Name:</th>
                                            <td>{{ $record->plans_name ?? 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <th>SLA:</th>
                                            <td>{{ $record->sla ?? 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Mode of Delivery:</th>
                                            <td>{{ $record->mode_of_delivery ?? 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status of Link:</th>
                                            <td>{{ $record->status_of_link ?? 'Not specified' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($record->purchaseOrder)
                    <!-- Individual Link Pricing (if available) -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="text-success mb-3"><i class="bi bi-list-ol"></i> Individual Link Pricing Details</h6>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Link #</th>
                                            <th>ARC Amount</th>
                                            <th>OTC Amount</th>
                                            <th>Static IP Cost</th>
                                            <th>Total per Link</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @for($i = 1; $i <= $record->purchaseOrder->no_of_links; $i++)
                                            @php
                                                $arcLink = $record->purchaseOrder->{"arc_link_{$i}"} ?? $record->purchaseOrder->arc_per_link;
                                                $otcLink = $record->purchaseOrder->{"otc_link_{$i}"} ?? $record->purchaseOrder->otc_per_link;
                                                $staticLink = $record->purchaseOrder->{"static_ip_link_{$i}"} ?? $record->purchaseOrder->static_ip_cost_per_link;
                                                $linkTotal = $arcLink + $otcLink + $staticLink;
                                            @endphp
                                            <tr>
                                                <td><strong>Link {{ $i }}</strong></td>
                                                <td>₹{{ number_format($arcLink, 2) }}</td>
                                                <td>₹{{ number_format($otcLink, 2) }}</td>
                                                <td>₹{{ number_format($staticLink, 2) }}</td>
                                                <td><strong>₹{{ number_format($linkTotal, 2) }}</strong></td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('operations.deliverables.edit', $record->id) }}" class="btn btn-primary">
                                    <i class="bi bi-pencil"></i> Edit Deliverable
                                </a>
                                @if($record->purchaseOrder)
                                <a href="{{ route('sm.purchaseorder.view', $record->purchaseOrder->id) }}" class="btn btn-success" target="_blank">
                                    <i class="bi bi-eye"></i> View Purchase Order
                                </a>
                                @endif
                                <a href="{{ route('operations.deliverables.open') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Back to List
                                </a>
                            </div>
                        </div>
                    </div>
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