@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header text-dark">
                    <h4 class="mb-0">
                        <i class="bi bi-plus-circle"></i> Create Purchase Order
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('sm.purchaseorder.store') }}" method="POST" id="purchaseOrderForm">
                        @csrf
                        
                        <div class="row">
                            {{-- Feasibility Selection --}}
                            <div class="col-md-6 mb-3">
                                <label for="feasibility_id" class="form-label">
                                    <strong>Feasibility Request ID <span class="text-danger">*</span></strong>
                                    <small class="text-muted">(Only unused feasibilities shown)</small>
                                </label>
                                <select class="form-select @error('feasibility_id') is-invalid @enderror" 
                                        id="feasibility_id" name="feasibility_id" required onchange="loadFeasibilityDetails()">
                                    <option value="">Select Available Feasibility</option>
                                    @forelse($closedFeasibilities as $feasibilityStatus)
                                        <option value="{{ $feasibilityStatus->feasibility->id }}" 
                                                {{ old('feasibility_id') == $feasibilityStatus->feasibility->id ? 'selected' : '' }}>
                                            {{ $feasibilityStatus->feasibility->feasibility_request_id }} - {{ $feasibilityStatus->feasibility->client->client_name ?? 'Unknown' }}
                                        </option>
                                    @empty
                                        <option value="" disabled>No unused closed feasibilities available</option>
                                    @endforelse
                                </select>
                                @if($closedFeasibilities->isEmpty())
                                    <div class="form-text text-warning">
                                        <i class="bi bi-info-circle"></i> All closed feasibilities already have purchase orders.
                                    </div>
                                @endif
                                @error('feasibility_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
            {{-- PO Number --}}
            <div class="col-md-6 mb-3">
                <label for="po_number" class="form-label">
                    <strong>PO Number <span class="text-danger">*</span></strong>
                </label>
                <input type="text" class="form-control @error('po_number') is-invalid @enderror" 
                       id="po_number" name="po_number" value="{{ old('po_number') }}" 
                       placeholder="Enter PO Number" required>
                @error('po_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
                            {{-- PO Date --}}
                            <div class="col-md-6 mb-3">
                                <label for="po_date" class="form-label">
                                    <strong>PO Date <span class="text-danger">*</span></strong>
                                </label>
                                <input type="date" class="form-control @error('po_date') is-invalid @enderror" 
                                       id="po_date" name="po_date" value="{{ old('po_date', date('Y-m-d')) }}" required>
                                @error('po_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- {{-- Client Details Display (Auto-filled from Feasibility) --}}
                        <div id="clientDetails" class="row mb-4 d-none">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h5 class="mb-0">Client Details (Auto-fetched from Feasibility)</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Company Name:</strong> <span id="clientCompanyName"></span></p>
                                                <p><strong>Contact Person:</strong> <span id="clientContactPerson"></span></p>
                                                <p><strong>Email:</strong> <span id="clientEmail"></span></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Phone:</strong> <span id="clientPhone"></span></p>
                                                <p><strong>Address:</strong> <span id="clientAddress"></span></p>
                                                <p><strong>GST Number:</strong> <span id="clientGST"></span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->

                        {{-- Purchase Order Details --}}
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="arc_per_link" class="form-label">
                                    <strong>ARC Per Link (₹) <span class="text-danger">*</span></strong>
                                </label>
                                <input type="number" step="0.01" min="0" class="form-control @error('arc_per_link') is-invalid @enderror" 
                                       id="arc_per_link" name="arc_per_link" value="{{ old('arc_per_link') }}" required onchange="calculateTotal()">
                                @error('arc_per_link')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="otc_per_link" class="form-label">
                                    <strong>OTC Per Link (₹) <span class="text-danger">*</span></strong>
                                </label>
                                <input type="number" step="0.01" min="0" class="form-control @error('otc_per_link') is-invalid @enderror" 
                                       id="otc_per_link" name="otc_per_link" value="{{ old('otc_per_link') }}" required onchange="calculateTotal()">
                                @error('otc_per_link')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="static_ip_cost_per_link" class="form-label">
                                    <strong>Static IP Cost Per Link (₹) <span class="text-danger">*</span></strong>
                                </label>
                                <input type="number" step="0.01" min="0" class="form-control @error('static_ip_cost_per_link') is-invalid @enderror" 
                                       id="static_ip_cost_per_link" name="static_ip_cost_per_link" value="{{ old('static_ip_cost_per_link') }}" required onchange="calculateTotal()">
                                @error('static_ip_cost_per_link')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="no_of_links" class="form-label">
                                    <strong>No. of Links <span class="text-danger">*</span></strong>
                                </label>
                                <input type="number" min="1" class="form-control @error('no_of_links') is-invalid @enderror" 
                                       id="no_of_links" name="no_of_links" value="{{ old('no_of_links') }}" required onchange="calculateTotal()">
                                @error('no_of_links')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="contract_period" class="form-label">
                                    <strong>Contract Period (Months) <span class="text-danger">*</span></strong>
                                </label>
                                <input type="number" min="1" class="form-control @error('contract_period') is-invalid @enderror" 
                                       id="contract_period" name="contract_period" value="{{ old('contract_period', 12) }}" required>
                                @error('contract_period')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <strong>Total Cost (Auto-calculated)</strong>
                                </label>
                                <div class="form-control bg-light" id="totalCost">₹0.00</div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="row">
                            <div class="col-12 text-end">
                                <a href="{{ route('sm.purchaseorder.index') }}" class="btn btn-secondary me-2">
                                    <i class="bi bi-arrow-left"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-save"></i> Create Purchase Order
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function loadFeasibilityDetails() {
    const feasibilityId = document.getElementById('feasibility_id').value;
    
    if (!feasibilityId) {
        document.getElementById('clientDetails').classList.add('d-none');
        document.getElementById('no_of_links').value = '';
        return;
    }

    fetch(`/sm/purchaseorder/feasibility/${feasibilityId}/details`)
        .then(response => response.json())
        .then(data => {
            // Display client details
            document.getElementById('clientCompanyName').textContent = data.client.company_name || 'N/A';
            document.getElementById('clientContactPerson').textContent = data.client.contact_person || 'N/A';
            document.getElementById('clientEmail').textContent = data.client.email || 'N/A';
            document.getElementById('clientPhone').textContent = data.client.phone || 'N/A';
            document.getElementById('clientAddress').textContent = data.client.address || 'N/A';
            document.getElementById('clientGST').textContent = data.client.gst_number || 'N/A';
            
            // Auto-fill number of links from feasibility
            document.getElementById('no_of_links').value = data.no_of_links;
            
            // Show client details
            document.getElementById('clientDetails').classList.remove('d-none');
            
            // Recalculate total
            calculateTotal();
        })
        .catch(error => {
            console.error('Error fetching feasibility details:', error);
            document.getElementById('clientDetails').classList.add('d-none');
        });
}

function calculateTotal() {
    const arc = parseFloat(document.getElementById('arc_per_link').value) || 0;
    const otc = parseFloat(document.getElementById('otc_per_link').value) || 0;
    const staticIP = parseFloat(document.getElementById('static_ip_cost_per_link').value) || 0;
    const links = parseInt(document.getElementById('no_of_links').value) || 0;
    
    const total = (arc + otc + staticIP) * links;
    document.getElementById('totalCost').textContent = `₹${total.toLocaleString('en-IN', { minimumFractionDigits: 2 })}`;
}
</script>
@endsection
