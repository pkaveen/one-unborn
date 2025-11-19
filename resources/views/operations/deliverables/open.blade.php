@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    @if($records->count() > 0)
        @foreach($records as $record)
        <div class="card shadow border-0 mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-truck me-2"></i>{{ $record->delivery_id }} - Deliverable Details</h5>
                <div>
                    <span class="badge bg-warning text-dark me-2">{{ $record->status }}</span>
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

                            <!-- ⭐ Newly added missing fields ⭐ -->
            <tr>
                <th class="bg-light">Delivered At</th>
                <td>{{ $record->delivered_at ?? 'N/A' }}</td>

                <th class="bg-light">Delivered By</th>
                <td>{{ $record->delivered_by ?? 'N/A' }}</td>
            </tr>

            <tr>
                <th class="bg-light">Purchase Order ID</th>
                <td colspan="3">{{ $record->purchase_order_id ?? 'N/A' }}</td>
            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Vendor Information from Feasibility Closed Status --}}
                @if($record->feasibility && $record->feasibility->feasibilityStatus)
                    @php $fs = $record->feasibility->feasibilityStatus; @endphp
                    <h6 class="text-success mb-3"><i class="bi bi-building"></i> Vendor Information (From Closed Feasibility)</h6>
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

                {{-- Link Details Form --}}
                <h6 class="text-info mb-3"><i class="bi bi-link-45deg"></i> Link Details</h6>
                <form action="{{ route('operations.deliverables.save', $record->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Site Address</label>
                            <input type="text" class="form-control" name="site_address" value="{{ $record->site_address }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Local Contact</label>
                            <input type="text" class="form-control" name="local_contact" value="{{ $record->local_contact }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">State</label>
                            <input type="text" class="form-control" name="state" value="{{ $record->state }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">GST Number</label>
                            <input type="text" class="form-control" name="gst_number" value="{{ $record->gst_number }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Link Type</label>
                            <input type="text" class="form-control" name="link_type" value="{{ $record->link_type }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Speed in Mbps</label>
                            <input type="text" class="form-control" name="speed_in_mbps" value="{{ $record->speed_in_mbps }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">No of Links</label>
                            <input type="number" class="form-control" name="no_of_links" value="{{ $record->no_of_links }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Vendor <span class="text-danger">*</span></label>
                            <select class="form-select" name="vendor" required>
                                <option value="">Select Vendor</option>
                                @if($record->feasibility && $record->feasibility->feasibilityStatus)
                                    @php $fs = $record->feasibility->feasibilityStatus; @endphp
                                    @if($fs->vendor1_name)
                                        <option value="{{ $fs->vendor1_name }}" {{ $record->vendor == $fs->vendor1_name ? 'selected' : '' }}>{{ $fs->vendor1_name }}</option>
                                    @endif
                                    @if($fs->vendor2_name)
                                        <option value="{{ $fs->vendor2_name }}" {{ $record->vendor == $fs->vendor2_name ? 'selected' : '' }}>{{ $fs->vendor2_name }}</option>
                                    @endif
                                    @if($fs->vendor3_name)
                                        <option value="{{ $fs->vendor3_name }}" {{ $record->vendor == $fs->vendor3_name ? 'selected' : '' }}>{{ $fs->vendor3_name }}</option>
                                    @endif
                                    @if($fs->vendor4_name)
                                        <option value="{{ $fs->vendor4_name }}" {{ $record->vendor == $fs->vendor4_name ? 'selected' : '' }}>{{ $fs->vendor4_name }}</option>
                                    @endif
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Circuit ID</label>
                            <input type="text" class="form-control" name="circuit_id" value="{{ $record->circuit_id }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Plans Name</label>
                            <input type="text" class="form-control" name="plans_name" value="{{ $record->plans_name }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Speed in Mbps Plan</label>
                            <input type="text" class="form-control" name="speed_in_mbps_plan" value="{{ $record->speed_in_mbps_plan }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">No of Months of Renewal</label>
                            <input type="number" class="form-control" name="no_of_months_renewal" value="{{ $record->no_of_months_renewal }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Date of Activation</label>
                            <input type="date" class="form-control" name="date_of_activation" value="{{ $record->date_of_activation }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">SLA</label>
                            <input type="text" class="form-control" name="sla" value="{{ $record->sla }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Mode of Delivery <span class="text-danger">*</span></label>
                            <select class="form-select" name="mode_of_delivery" id="mode_of_delivery_{{ $record->id }}" data-record-id="{{ $record->id }}" onchange="toggleDeliveryMode(this.getAttribute('data-record-id'))" required>
                                <option value="">Select Mode</option>
                                <option value="DHCP" {{ $record->mode_of_delivery == 'DHCP' ? 'selected' : '' }}>DHCP</option>
                                <option value="Static IP" {{ $record->mode_of_delivery == 'Static IP' ? 'selected' : '' }}>Static IP</option>
                                <option value="PPPoE" {{ $record->mode_of_delivery == 'PPPoE' ? 'selected' : '' }}>PPPoE</option>
                            </select>
                        </div>
                    </div>

                    {{-- PPPoE Fields --}}
                    <div id="pppoe_fields_{{ $record->id }}" class="delivery-mode-section {{ $record->mode_of_delivery == 'PPPoE' ? '' : 'd-none' }}" data-mode="PPPoE">
                        <div class="alert alert-warning"><strong>PPPoE Configuration</strong></div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Username</label>
                                <input type="text" class="form-control" name="pppoe_username" value="{{ $record->pppoe_username }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">VLAN</label>
                                <input type="text" class="form-control" name="pppoe_vlan" value="{{ $record->static_vlan }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Password</label>
                                <input type="password" class="form-control" name="pppoe_password" value="{{ $record->pppoe_password }}">
                            </div>
                        </div>
                    </div>

                    {{-- DHCP Fields --}}
                    <div id="dhcp_fields_{{ $record->id }}" class="delivery-mode-section {{ $record->mode_of_delivery == 'DHCP' ? '' : 'd-none' }}" data-mode="DHCP">
                        <div class="alert alert-info"><strong>DHCP Configuration</strong></div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">IP Address</label>
                                <input type="text" class="form-control" name="dhcp_ip_address" value="{{ $record->static_ip_address }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">VLAN</label>
                                <input type="text" class="form-control" name="dhcp_vlan" value="{{ $record->static_vlan }}">
                            </div>
                        </div>
                    </div>

                    {{-- Static IP Fields --}}
                    <div id="static_fields_{{ $record->id }}" class="delivery-mode-section {{ $record->mode_of_delivery == 'Static IP' ? '' : 'd-none' }}" data-mode="Static IP">
                        <div class="alert alert-success"><strong>Static IP Configuration</strong></div>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">IP Address</label>
                                <input type="text" class="form-control" name="static_ip_address" value="{{ $record->static_ip_address }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Subnet Mask</label>
                                <input type="text" class="form-control" name="static_subnet_mask" value="{{ $record->static_subnet_mask }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Gateway</label>
                                <input type="text" class="form-control" name="static_gateway" value="{{ $record->static_gateway }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">VLAN</label>
                                <input type="text" class="form-control" name="static_vlan_tag" value="{{ $record->static_vlan_tag }}">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status of the Link</label>
                            <input type="text" class="form-control" name="status_of_link" value="{{ $record->status_of_link }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">OTC (Extra if)</label>
                            <input type="number" step="0.01" class="form-control" name="otc_extra_charges" value="{{ $record->otc_extra_charges }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Upload OTC Bill</label>
                            <input type="file" class="form-control" name="otc_bill_file">
                            @if($record->otc_bill_file)
                                <small class="text-muted">Current: {{ $record->otc_bill_file }}</small>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <button type="submit" name="action" value="save" class="btn btn-outline-secondary">
                            <i class="bi bi-save"></i> Save Draft
                        </button>
                        <div class="d-flex gap-2">
                            <button type="submit" name="action" value="move_to_progress" class="btn btn-primary">
                                <i class="bi bi-arrow-right-circle"></i> Save & Move to In Progress
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endforeach
    @else
        <div class="card shadow border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-hourglass-split me-2"></i>Open Deliverables</h5>
            </div>
            <div class="card-body text-center py-5">
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
        </div>
    @endif
</div>

<script>
function toggleDeliveryMode(recordId) {
    const mode = document.getElementById('mode_of_delivery_' + recordId).value;
    
    // Get all sections
    const pppoeFields = document.getElementById('pppoe_fields_' + recordId);
    const dhcpFields = document.getElementById('dhcp_fields_' + recordId);
    const staticFields = document.getElementById('static_fields_' + recordId);
    
    // Hide all sections by adding d-none class
    pppoeFields.classList.add('d-none');
    dhcpFields.classList.add('d-none');
    staticFields.classList.add('d-none');
    
    // Show selected section by removing d-none class
    if (mode === 'PPPoE') {
        pppoeFields.classList.remove('d-none');
    } else if (mode === 'DHCP') {
        dhcpFields.classList.remove('d-none');
    } else if (mode === 'Static IP') {
        staticFields.classList.remove('d-none');
    }
}
</script>

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