@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header text-dark d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-receipt"></i> Purchase Orders
                    </h4>
                    <a href="{{ route('sm.purchaseorder.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i> Create New Purchase Order
                        </a>
                </div>
                <div class="card-body">
                    <!-- {{-- Action Buttons --}}
                    <div class="mb-3">
                        <a href="{{ route('sm.purchaseorder.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i> Create New Purchase Order
                        </a>
                    </div> -->

                    {{-- Success/Error Messages --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Purchase Orders Table --}}
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="table-dark-primary">
                                <tr>
                                    <th>S.No</th>
                                    <th>Actions</th>
                                    <th>PO Number</th>
                                    <th>PO Date</th>
                                    <th>Client Name</th>
                                    <th>Feasibility ID</th>
                                    <th>No. of Links</th>
                                    <th>Total Cost</th>
                                    <th>Status</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseOrders as $index => $po)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="text-center d-flex justify-content-center gap-1">
                                {{-- Edit --}}
                                @if($permissions->can_edit)
                               <a href="{{ route('sm.purchaseorder.edit', $po->id) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif

                                {{-- Toggle Status --}}
                                @if($permissions->can_edit)
                                <form action="{{ route('sm.purchaseorder.toggleStatus', $po->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm {{ $po->status === 'Active' ? 'btn-warning' : 'btn-success' }}" 
                                            onclick="return confirm('Are you sure you want to change the status of this Purchase Order?')"
                                            title="{{ $po->status === 'Active' ? 'Deactivate' : 'Activate' }}">
                                        @if($po->status === 'Active')
                                            <i class="bi bi-pause-circle"></i>
                                        @else
                                            <i class="bi bi-play-circle"></i>
                                        @endif
                                    </button>
                                </form>
                                @endif

                                 {{-- Delete --}}
                                 @if($permissions->can_delete)
                                 <form action="{{ route('sm.purchaseorder.destroy',$po->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE') 
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this Purchase Order?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                   @endif

                                   
                                {{-- View --}}
                                   @if($permissions->can_view)
                                   <a href="{{ route('sm.purchaseorder.view', $po->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-eye"></i>
                                    </a>
                                     @endif

                            </td>
                                        <!-- <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('sm.purchaseorder.view', $po->id) }}" 
                                                   class="btn btn-info btn-sm" title="View">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                                @if($po->status === 'Draft')
                                                    <a href="{{ route('sm.purchaseorder.edit', $po->id) }}" 
                                                       class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </a>
                                                    <form action="{{ route('sm.purchaseorder.submit', $po->id) }}" 
                                                          method="POST" class="d-inline" 
                                                          onsubmit="return confirm('Are you sure you want to submit this PO?')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm" title="Submit">
                                                            <i class="bi bi-check-circle"></i> Submit
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('sm.purchaseorder.destroy', $po->id) }}" 
                                                          method="POST" class="d-inline" 
                                                          onsubmit="return confirm('Are you sure you want to delete this PO?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                            <i class="bi bi-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td> -->
                                        <td>
                                            <strong class="text-primary">{{ $po->po_number }}</strong>
                                        </td>
                                        <td>{{ $po->po_date->format('d-m-Y') }}</td>
                                        <td>{{ $po->feasibility->client->client_name ?? 'N/A' }}</td>
                                        <td>{{ $po->feasibility->feasibility_request_id ?? 'N/A' }}</td>
                                        <td>{{ $po->no_of_links }}</td>
                                        <td>
                                            ₹{{ number_format(($po->arc_per_link + $po->otc_per_link + $po->static_ip_cost_per_link) * $po->no_of_links, 2) }}
                                        </td>
                        <td>
                            @if($po->status === 'Active')
                                <span class="badge bg-success">{{ $po->status }}</span>
                            @else
                                <span class="badge bg-danger">{{ $po->status }}</span>
                            @endif
                        </td>                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">
                                            <i class="bi bi-inbox"></i> No Purchase Orders found. 
                                            <a href="{{ route('sm.purchaseorder.create') }}" class="text-decoration-none">Create your first Purchase Order</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
