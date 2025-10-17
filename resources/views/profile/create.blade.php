@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

           {{-- ✅ Show Validation Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif@endif

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Create Your Profile</h5>
                </div>

                <div class="card-body">
                    <!-- IMPORTANT: enctype for file upload -->
                    <form method="POST" action="{{ route('profile.store') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- Basic Info --}}
                        <div class="mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" name="fname" value="{{ old('fname') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="lname" value="{{ old('lname') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Designation</label>
                            <input type="text" class="form-control" name="designation" value="{{ old('designation') }}" required>
                        </div>

                        {{-- Address --}}
                        <h5 class="text-secondary mt-3">Address</h5>
                        <input type="text" name="address1" class="form-control mb-2" placeholder="Address Line 1">
                        <input type="text" name="address2" class="form-control mb-2" placeholder="Address Line 2">
                        <input type="text" name="address3" class="form-control mb-2" placeholder="Address Line 3">

                        {{-- Date of Birth --}}
                        <div class="mb-3">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="Date_of_Birth" class="form-control" placeholder="select DOB" value="{{ old('Date_of_Birth') }}" required>

                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>

                        {{-- Phone Numbers --}}
                        <div class="mb-3">
                            <label class="form-label">Phone Number 1</label>
                            <input type="number" class="form-control" name="phone1" value="{{ old('phone1') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone Number 2</label>
                            <input type="number" class="form-control" name="phone2" value="{{ old('phone2') }}">
                        </div>

                        {{-- Aadhaar --}}
                        <div class="mb-3">
                            <label class="form-label">Aadhaar Number</label>
                            <input type="number" class="form-control" name="aadhaar_number" value="{{ old('aadhaar_number') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Aadhaar Upload</label>
                            <input type="file" class="form-control" name="aadhaar_upload" required>
                        </div>

                        {{-- PAN --}}
                        <div class="mb-3">
                            <label class="form-label">PAN Number</label>
                            <input type="text" name="pan" class="form-control mb-2" value="{{ old('pan') }}" placeholder="PAN No">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">PAN Upload</label>
                            <input type="file" class="form-control" name="pan_upload" required>
                        </div>

                        {{-- Bank Details --}}
                        <h5 class="text-secondary mt-3">Bank Details</h5>
                        <input type="text" name="bank_name" class="form-control mb-2" placeholder="Bank Name">
                        <input type="text" name="branch" class="form-control mb-2" placeholder="Branch">
                        <input type="text" name="bank_account_no" class="form-control mb-2" placeholder="Account No">
                        <input type="text" name="ifsc_code" class="form-control mb-3" placeholder="IFSC Code">

                        <button type="submit" class="btn btn-success w-100">Save Profile</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
