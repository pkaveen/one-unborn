@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-eye"></i> Purchase Order Details - {{ $purchaseOrder->po_number }}
                    </h4>
                </div>
                <div class="card-body">
                    {{-- PO Header Information --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">PO Number:</th>
                                    <td><strong class="text-primary">{{ $purchaseOrder->po_number }}</strong></td>
                                </tr>
                                <tr>
                                    <th>PO Date:</th>
                                    <td>{{ $purchaseOrder->po_date->format('d-m-Y') }}</td>
                                </tr>
                                <!-- <tr>
                                    <th>Status:</th>
                                    <td>
                                        @if($purchaseOrder->status === 'Draft')
                                            <span class="badge bg-warning">{{ $purchaseOrder->status }}</span>
                                        @elseif($purchaseOrder->status === 'Submitted')
                                            <span class="badge bg-info">{{ $purchaseOrder->status }}</span>
                                        @elseif($purchaseOrder->status === 'Approved')
                                            <span class="badge bg-success">{{ $purchaseOrder->status }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ $purchaseOrder->status }}</span>
                                        @endif
                                    </td>
                                </tr> -->

                                <tr>
                                    <th>Feasibility ID:</th>
                                    <td>{{ $purchaseOrder->feasibility->feasibility_request_id ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <!-- <tr>
                                    <th width="40%">Created On:</th>
                                    <td>{{ $purchaseOrder->created_at->format('d-m-Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Modified:</th>
                                    <td>{{ $purchaseOrder->updated_at->format('d-m-Y H:i:s') }}</td>
                                </tr> -->
                                <tr>
                                    <th>Contract Period:</th>
                                    <td>{{ $purchaseOrder->contract_period }} Months</td>
                                </tr>
                                <tr>
                                    <th>No. of Links:</th>
                                    <td>{{ $purchaseOrder->no_of_links }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
<!-- 
                    {{-- Client Information --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="bi bi-building"></i> Client Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Company Name:</th>
                                            <td>{{ $purchaseOrder->feasibility->client->company_name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Contact Person:</th>
                                            <td>{{ $purchaseOrder->feasibility->client->contact_person ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email:</th>
                                            <td>{{ $purchaseOrder->feasibility->client->email ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Phone:</th>
                                            <td>{{ $purchaseOrder->feasibility->client->phone ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Address:</th>
                                            <td>{{ $purchaseOrder->feasibility->client->address ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>GST Number:</th>
                                            <td>{{ $purchaseOrder->feasibility->client->gst_number ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>City:</th>
                                            <td>{{ $purchaseOrder->feasibility->client->city ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>State:</th>
                                            <td>{{ $purchaseOrder->feasibility->client->state ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div> -->
<!-- 
                    {{-- Feasibility Details --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="bi bi-clipboard-data"></i> Feasibility Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Service Type:</th>
                                            <td>{{ $purchaseOrder->feasibility->type_of_service ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Location:</th>
                                            <td>{{ $purchaseOrder->feasibility->area ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Pincode:</th>
                                            <td>{{ $purchaseOrder->feasibility->pincode ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Speed:</th>
                                            <td>{{ $purchaseOrder->feasibility->speed ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Static IP:</th>
                                            <td>{{ $purchaseOrder->feasibility->static_ip ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Vendor Type:</th>
                                            <td>{{ $purchaseOrder->feasibility->vendor_type ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Expected Delivery:</th>
                                            <td>{{ $purchaseOrder->feasibility->expected_delivery ? $purchaseOrder->feasibility->expected_delivery->format('d-m-Y') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Expected Activation:</th>
                                            <td>{{ $purchaseOrder->feasibility->expected_activation ? $purchaseOrder->feasibility->expected_activation->format('d-m-Y') : 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div> -->

                    {{-- Pricing Details --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="bi bi-currency-rupee"></i> Pricing Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <table class="table table-bordered">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Description</th>
                                                <th>Per Link (₹)</th>
                                                <th>No. of Links</th>
                                                <th>Total Amount (₹)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>ARC (Annual Rental Charges)</td>
                                                <td class="text-end">{{ number_format($purchaseOrder->arc_per_link, 2) }}</td>
                                                <td class="text-center">{{ $purchaseOrder->no_of_links }}</td>
                                                <td class="text-end">{{ number_format($purchaseOrder->arc_per_link * $purchaseOrder->no_of_links, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td>OTC (One Time Charges)</td>
                                                <td class="text-end">{{ number_format($purchaseOrder->otc_per_link, 2) }}</td>
                                                <td class="text-center">{{ $purchaseOrder->no_of_links }}</td>
                                                <td class="text-end">{{ number_format($purchaseOrder->otc_per_link * $purchaseOrder->no_of_links, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Static IP Cost</td>
                                                <td class="text-end">{{ number_format($purchaseOrder->static_ip_cost_per_link, 2) }}</td>
                                                <td class="text-center">{{ $purchaseOrder->no_of_links }}</td>
                                                <td class="text-end">{{ number_format($purchaseOrder->static_ip_cost_per_link * $purchaseOrder->no_of_links, 2) }}</td>
                                            </tr>
                                            <tr class="table-success">
                                                <th colspan="3">Total Amount</th>
                                                <th class="text-end">
                                                    ₹{{ number_format(($purchaseOrder->arc_per_link + $purchaseOrder->otc_per_link + $purchaseOrder->static_ip_cost_per_link) * $purchaseOrder->no_of_links, 2) }}
                                                </th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- {{-- Remarks --}}
                    @if($purchaseOrder->remarks)
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="bi bi-chat-text"></i> Remarks</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">{{ $purchaseOrder->remarks }}</p>
                            </div>
                        </div>
                    @endif -->

                    {{-- Action Buttons --}}
                    <div class="row">
                        <div class="col-12 text-end">
                            <a href="{{ route('sm.purchaseorder.index') }}" class="btn btn-secondary me-2">
                                <i class="bi bi-arrow-left"></i> Back to List
                            </a>
                            <!-- @if($purchaseOrder->status === 'Draft')
                                <a href="{{ route('sm.purchaseorder.edit', $purchaseOrder->id) }}" class="btn btn-warning me-2">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form action="{{ route('sm.purchaseorder.submit', $purchaseOrder->id) }}" 
                                      method="POST" class="d-inline" 
                                      onsubmit="return confirm('Are you sure you want to submit this Purchase Order?')">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle"></i> Submit
                                    </button>
                                </form>
                            @endif -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection