@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-3 text-primary">View Company</h3>

    <div class="card shadow border-0 p-4">
        <table class="table table-bordered">
            <tr>
                <th>Company Name</th>
                <td>{{ $company->company_name }}</td>
            </tr>
            <tr>
                <th>CIN / LLPIN</th>
                <td>{{ $company->cin_llpin ?? '-' }}</td>
            </tr>
            <tr>
                <th>Contact No</th>
                <td>{{ $company->contact_no ?? '-' }}</td>
            </tr>
            <tr>
                <th>Phone No</th>
                <td>{{ $company->phone_no ?? '-' }}</td>
            </tr>
            <tr>
                <th>Email 1</th>
                <td>{{ $company->email_1 ?? '-' }}</td>
            </tr>
            <tr>
                <th>Email 2</th>
                <td>{{ $company->email_2 ?? '-' }}</td>
            </tr>
            <tr>
                <th>Address</th>
                <td>{{ $company->address ?? '-' }}</td>
            </tr>

            <tr>
                <th>Billing Logo</th>
                <td>
                    @if(!empty($company->billing_logo))
                        <img src="{{ asset('storage/'.$company->billing_logo) }}" width="120" class="border rounded">
                    @else
                        -
                    @endif
                </td>
            </tr>

            <tr>
                <th>Normal Sign</th>
                <td>
                    @if(!empty($company->billing_sign_normal))
                        <img src="{{ asset('storage/'.$company->billing_sign_normal) }}" width="120" class="border rounded">
                    @else
                        -
                    @endif
                </td>
            </tr>

            <tr>
                <th>Digital Sign</th>
                <td>
                    @if(!empty($company->billing_sign_digital))
                        <img src="{{ asset('storage/'.$company->billing_sign_digital) }}" width="120" class="border rounded">
                    @else
                        -
                    @endif
                </td>
            </tr>

            <tr>
                <th>GST No</th>
                <td>{{ $company->gst_no ?? '-' }}</td>
            </tr>
            <tr>
                <th>PAN Number</th>
                <td>{{ $company->pan_number ?? '-' }}</td>
            </tr>
            <tr>
                <th>TAN Number</th>
                <td>{{ $company->tan_number ?? '-' }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <span class="badge {{ $company->status === 'Active' ? 'bg-success' : 'bg-danger' }}">
                        {{ $company->status }}
                    </span>
                </td>
            </tr>
        </table>

        <div class="text-end">
            <a href="{{ route('companies.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection
