@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    @if($records->count() > 0)
        @foreach($records as $record)
        <div class="card shadow border-0 mb-4">
            <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-hourglass-half me-2"></i>{{ $record->delivery_id }} - Deliverable In Progress</h5>
                <div>
                    <span class="badge bg-dark me-2">{{ $record->status }}</span>
                    <a href="{{ route('operations.deliverables.edit', $record->id) }}" class="btn btn-light btn-sm">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                </div>
            </div>

            <div class="card-body">
                {{-- Summary Information Table --}}
                <h6 class="text-primary mb-3"><i class="bi bi-info-circle"></i> Summary Information</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th width="25%" class="bg-light">Feasibility Request Number</th>
                                <td width="25%">{{ $record->feasibility->feasibility_request_id ?? 'N/A' }}</td>
                                <th width="25%" class="bg-light">PO Number</th>
                                <td width="25%">{{ $record->purchaseOrder->po_number ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Company Name</th>
                                <td>{{ $record->feasibility->client->client_name ?? 'N/A' }}</td>
                                <th class="bg-light">Company Address</th>
                                <td>{{ $record->feasibility->address ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Speed</th>
                                <td>{{ $record->feasibility->speed ?? 'N/A' }}</td>
                                <th class="bg-light">No. of Links</th>
                                <td>{{ $record->purchaseOrder->no_of_links ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Type of Links</th>
                                <td>{{ $record->feasibility->connection_type ?? $record->link_type ?? 'N/A' }}</td>
                                <th class="bg-light">SPOC Name</th>
                                <td>{{ $record->feasibility->spoc_name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">SPOC Contact</th>
                                <td colspan="3">{{ $record->feasibility->spoc_contact1 ?? 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Vendor Information from Feasibility Closed Status --}}
                @if($record->feasibility && $record->feasibility->feasibilityStatus)
                    @php $fs = $record->feasibility->feasibilityStatus; @endphp
                    <h6 class="text-success mb-3"><i class="bi bi-building"></i> Vendor Information</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th>Vendor</th>
                                    <th>ARC</th>
                                    <th>OTC</th>
                                    <th>Static IP Cost</th>
                                    <th>Delivery Timeline</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($fs->vendor1_name)
                                <tr>
                                    <td><strong>{{ $fs->vendor1_name }}</strong></td>
                                    <td>₹{{ number_format($fs->vendor1_arc ?? 0, 2) }}</td>
                                    <td>₹{{ number_format($fs->vendor1_otc ?? 0, 2) }}</td>
                                    <td>₹{{ number_format($fs->vendor1_static_ip_cost ?? 0, 2) }}</td>
                                    <td>{{ $fs->vendor1_delivery_timeline ?? '-' }}</td>
                                </tr>
                                @endif
                                @if($fs->vendor2_name)
                                <tr>
                                    <td><strong>{{ $fs->vendor2_name }}</strong></td>
                                    <td>₹{{ number_format($fs->vendor2_arc ?? 0, 2) }}</td>
                                    <td>₹{{ number_format($fs->vendor2_otc ?? 0, 2) }}</td>
                                    <td>₹{{ number_format($fs->vendor2_static_ip_cost ?? 0, 2) }}</td>
                                    <td>{{ $fs->vendor2_delivery_timeline ?? '-' }}</td>
                                </tr>
                                @endif
                                @if($fs->vendor3_name)
                                <tr>
                                    <td><strong>{{ $fs->vendor3_name }}</strong></td>
                                    <td>₹{{ number_format($fs->vendor3_arc ?? 0, 2) }}</td>
                                    <td>₹{{ number_format($fs->vendor3_otc ?? 0, 2) }}</td>
                                    <td>₹{{ number_format($fs->vendor3_static_ip_cost ?? 0, 2) }}</td>
                                    <td>{{ $fs->vendor3_delivery_timeline ?? '-' }}</td>
                                </tr>
                                @endif
                                @if($fs->vendor4_name)
                                <tr>
                                    <td><strong>{{ $fs->vendor4_name }}</strong></td>
                                    <td>₹{{ number_format($fs->vendor4_arc ?? 0, 2) }}</td>
                                    <td>₹{{ number_format($fs->vendor4_otc ?? 0, 2) }}</td>
                                    <td>₹{{ number_format($fs->vendor4_static_ip_cost ?? 0, 2) }}</td>
                                    <td>{{ $fs->vendor4_delivery_timeline ?? '-' }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                @endif

                {{-- Link Details --}}
                <h6 class="text-warning mb-3"><i class="bi bi-link-45deg"></i> Link Details</h6>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th width="25%" class="bg-light">Site Address</th>
                                <td width="75%" colspan="3">{{ $record->site_address ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Vendor</th>
                                <td>{{ $record->vendor ?? '-' }}</td>
                                <th class="bg-light">Circuit ID</th>
                                <td>{{ $record->circuit_id ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Mode of Delivery</th>
                                <td colspan="3">{{ $record->mode_of_delivery ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <form action="{{ route('operations.deliverables.save', $record->id) }}" method="POST">
                    @csrf
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <a href="{{ route('operations.deliverables.edit', $record->id) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil"></i> Edit Details
                        </a>
                        <button type="submit" name="action" value="complete" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Mark as Delivered
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endforeach
    @else
        <div class="card shadow border-0">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-hourglass-half me-2"></i>In Progress Deliverables</h5>
            </div>
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-hourglass-half display-1 text-muted"></i>
                </div>
                <h5 class="text-muted">No In Progress Deliverables Found</h5>
                <p class="text-muted">There are currently no deliverables in "In Progress" status.</p>
            </div>
        </div>
    @endif
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