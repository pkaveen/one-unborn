@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-3 text-primary">View Client Details</h3>

    <div class="card shadow border-0 p-4">
        {{-- Basic Details --}}
        <h5 class="text-secondary">Basic Details</h5>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">Client Name:</label>
                <p class="form-control-plaintext">{{ $client->client_name }}</p>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Client Code:</label>
                <p class="form-control-plaintext">{{ $client->client_code }}</p>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Business Display Name:</label>
            <p class="form-control-plaintext">{{ $client->business_display_name }}</p>
        </div>

        {{-- Address --}}
        <h5 class="text-secondary mt-3">Address</h5>
        <p class="form-control-plaintext mb-0">{{ $client->address1 }}</p>
        <p class="form-control-plaintext mb-0">{{ $client->address2 }}</p>
        <p class="form-control-plaintext mb-2">{{ $client->address3 }}</p>

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">City:</label>
                <p class="form-control-plaintext">{{ $client->city }}</p>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">State:</label>
                <p class="form-control-plaintext">{{ $client->state }}</p>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Country:</label>
                <p class="form-control-plaintext">{{ $client->country }}</p>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Pincode:</label>
            <p class="form-control-plaintext">{{ $client->pincode }}</p>
        </div>

        {{-- Business Contact --}}
        <h5 class="text-secondary mt-3">Business Contact Details</h5>
        <div class="row">
            <div class="col-md-4">
                <label class="form-label fw-bold">Billing SPOC Name:</label>
                <p class="form-control-plaintext">{{ $client->billing_spoc_name }}</p>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Contact Number:</label>
                <p class="form-control-plaintext">{{ $client->billing_spoc_contact }}</p>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Email:</label>
                <p class="form-control-plaintext">{{ $client->billing_spoc_email }}</p>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">GSTIN:</label>
            <p class="form-control-plaintext">{{ $client->gstin }}</p>
        </div>

        {{-- Invoice Details --}}
        <h5 class="text-secondary mt-3">Invoice Details</h5>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">Invoice Email:</label>
                <p class="form-control-plaintext">{{ $client->invoice_email }}</p>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Invoice CC:</label>
                <p class="form-control-plaintext">{{ $client->invoice_cc }}</p>
            </div>
        </div>

        {{-- Technical Support --}}
        <h5 class="text-secondary mt-3">Technical Support</h5>
        <div class="row">
            <div class="col-md-4">
                <label class="form-label fw-bold">SPOC Name:</label>
                <p class="form-control-plaintext">{{ $client->support_spoc_name }}</p>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Mobile:</label>
                <p class="form-control-plaintext">{{ $client->support_spoc_mobile }}</p>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Email:</label>
                <p class="form-control-plaintext">{{ $client->support_spoc_email }}</p>
            </div>
        </div>

        <div class="mt-3">
            <label class="form-label fw-bold">Status:</label>
            <span class="badge bg-{{ $client->status == 'Active' ? 'success' : 'secondary' }}">
                {{ $client->status }}
            </span>
        </div>

        <div class="mt-4">
            <a href="{{ route('clients.index') }}" class="btn btn-secondary">Back</a>
            <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-primary">Edit Client</a>
        </div>
    </div>
</div>
@endsection
