@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    @if($records->count() > 0)
        @foreach($records as $record)
        <div class="card shadow border-0 mb-4">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-check-circle me-2"></i>{{ $record->delivery_id }} - Delivered</h5>
                <div>
                    <span class="badge bg-light text-dark me-2">{{ $record->status }}</span>
                    <a href="{{ route('operations.deliverables.edit', $record->id) }}" class="btn btn-light btn-sm">
                        <i class="bi bi-eye"></i> View
                    </a>
                </div>
            </div>

            <div class="card-body">
                {{-- Summary Information --}}
                <h6 class="text-primary mb-3"><i class="bi bi-info-circle"></i> Summary Information</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th width="25%" class="bg-light">Feasibility Request</th>
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
                                <th class="bg-light">Vendor</th>
                                <td>{{ $record->vendor ?? '-' }}</td>
                                <th class="bg-light">Circuit ID</th>
                                <td>{{ $record->circuit_id ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Mode of Delivery</th>
                                <td>{{ $record->mode_of_delivery ?? '-' }}</td>
                                <th class="bg-light">Date of Activation</th>
                                <td>{{ $record->date_of_activation ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Delivery Information --}}
                @if($record->delivered_at)
                <div class="alert alert-success">
                    <h6 class="alert-heading"><i class="bi bi-check-circle"></i> Delivery Information</h6>
                    <hr>
                    <p class="mb-1"><strong>Delivered At:</strong> {{ \Carbon\Carbon::parse($record->delivered_at)->format('d M Y, h:i A') }}</p>
                    @if($record->delivered_by)
                        <p class="mb-1"><strong>Delivered By:</strong> {{ $record->delivered_by }}</p>
                    @endif
                    @if($record->delivery_notes)
                        <p class="mb-0"><strong>Notes:</strong> {{ $record->delivery_notes }}</p>
                    @endif
                </div>
                @endif

                <div class="d-flex justify-content-end">
                    <a href="{{ route('operations.deliverables.edit', $record->id) }}" class="btn btn-primary">
                        <i class="bi bi-eye"></i> View Full Details
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    @else
        <div class="card shadow border-0">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-truck me-2"></i>Delivered Deliverables</h5>
            </div>
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-truck display-1 text-muted"></i>
                </div>
                <h5 class="text-muted">No Delivered Deliverables Found</h5>
                <p class="text-muted">There are currently no deliverables in "Delivery" status.</p>
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