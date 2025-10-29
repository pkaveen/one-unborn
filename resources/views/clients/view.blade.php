@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-3 text-primary">View Company</h3>

    <div class="card shadow border-0 p-4">
        <table class="table table-bordered">
            <tr>
                <th>Client Name</th>
                <td>{{ $client->client_name ?? '-' }}</td>
            </tr>
            <tr>
                <th>Client Code</th>
                <td>{{ $client->client_code ?? '-' }}</td>
            </tr>
            <tr>
                <th>Business Display Name</th>
                <td>{{ $client->business_display_name ?? '-' }}</td>
            </tr>
            <tr>
                <th>Address</th>
                <td>{{ $client->address1 ?? '-' }}</td>
            </tr>
            <tr>
                <th>Address</th>
                <td>{{ $client->address2 ?? '-' }}</td>
            </tr>
            <tr>
                <th>Address</th>
                <td>{{ $client->address3 ?? '-' }}</td>
            </tr>
            <tr>
                <th>City</th>
                <td>{{ $client->city ?? '-' }}</td>
            </tr>
            <tr>
                <th>State</th>
                <td>{{ $client->state ?? '-' }}</td>
            </tr>
            <tr>
                <th>Country</th>
                <td>{{ $client->country ?? '-' }}</td>
            </tr>
            <tr>
                <th>Pincode</th>
                <td>{{ $client->pincode ?? '-' }}</td>
            </tr>
            <tr>
                <th>Billing SPOC Name</th>
                <td>{{ $client->billing_spoc_name ?? '-' }}</td>
            </tr>
            <tr>
                <th>Contact Number</th>
                <td>{{ $client->billing_spoc_contact ?? '-' }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $client->billing_spoc_email ?? '-' }}</td>
            </tr>
            <tr>
                <th>GSTIN</th>
                <td>{{ $client->gstin ?? '-' }}</td>
            </tr>
            <tr>
                <th>Invoice Email</th>
                <td>{{ $client->invoice_email ?? '-' }}</td>
            </tr>
            <tr>
                <th>Invoice CC</th>
                <td>{{ $client->invoice_cc ?? '-' }}</td>
            </tr>
            <tr>
                <th>SPOC Name</th>
                <td>{{ $client->support_spoc_name ?? '-' }}</td>
            </tr>
            <tr>
                <th>Mobile</th>
                <td>{{ $client->support_spoc_mobile ?? '-' }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $client->support_spoc_email ?? '-' }}</td>
            </tr>

            <tr>
                <th>Status</th>
                <td>
                    <span class="badge {{ $client->status === 'Active' ? 'bg-success' : 'bg-danger' }}">
                        {{ $client->status }}
                    </span>
                </td>
            </tr>
        </table>

        <div class="text-end">
            <a href="{{ route('clients.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection