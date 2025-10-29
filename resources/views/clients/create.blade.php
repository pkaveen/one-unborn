@extends('layouts.app') {{-- ✅ Extends main app layout (header, sidebar, etc.) --}}

@section('content')
<div class="container py-4"> {{-- ✅ Bootstrap container with vertical padding --}}
    <h3 class="mb-3 text-primary">Add Client</h3>
    
    <div class="card shadow border-0 p-4"> {{-- ✅ Card for a neat box UI --}}
    
        {{-- ⚠️ Display validation errors if any --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li> {{-- List each validation error --}}
                    @endforeach
                </ul>
            </div>
        @endif

        {{--  Client creation form --}}
        <form action="{{ route('clients.store') }}" method="POST">
            @csrf {{--  Protect against CSRF attacks --}}
            <div class="col-md-4">
    <label class="form-label">PAN Number</label>
    <input type="text" id="pan_number" name="pan_number" class="form-control" 
           placeholder="Enter PAN Number">
           <!-- <button type="button" id="verifyPanBtn" class="btn btn-primary">Verify</button> -->
</div>
<!-- 👇 ADD THIS LINE (important!) -->
  <small id="panStatus" class="text-muted mt-1 d-block"></small>


            {{--  Basic Details Section --}}
            <h5 class="text-secondary">Basic Details</h5>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Client Name</label>
                    <input type="text" name="client_name" class="form-control" 
                           value="{{ old('client_name') }}" required> {{--  Retains old input on validation error --}}
                </div>
                <div class="col-md-6">
                    <label class="form-label">Client Code</label>
                    <input type="text" class="form-control" value="Auto Generated" readonly> {{--  Display only --}}
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Business Display Name</label>
                <input type="text" name="business_display_name" id="business_display_name" class="form-control"
                       value="{{ old('business_display_name') }}">
            </div>

            {{--  Address Section --}}
            <h5 class="text-secondary mt-3">Address</h5>
            <input type="text" name="address1" id="address1" class="form-control mb-2" placeholder="Address Line 1"
                   value="{{ old('address1') }}">
            <input type="text" name="address2" class="form-control mb-2" placeholder="Address Line 2"
                   value="{{ old('address2') }}">
            <input type="text" name="address3" class="form-control mb-2" placeholder="Address Line 3"
                   value="{{ old('address3') }}">

            {{--  City, State, Country Dropdowns with Select2 tagging --}}
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">City</label>
                    <select name="city" id="city" class="form-select select2-tags">
                        <option value="">Select or Type City</option>
                        <option value="Bangalore">Bangalore</option>
                        <option value="Chennai">Chennai</option>
                        <option value="Hyderabad">Hyderabad</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">State</label>
                    <select name="state" id="state" class="form-select select2-tags">
                        <option value="">Select or Type State</option>
                        <option value="Karnataka">Karnataka</option>
                        <option value="Tamil Nadu">Tamil Nadu</option>
                        <option value="Telangana">Telangana</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Country</label>
                    <select name="country" id="country" class="form-select select2-tags">
                        <option value="">Select or Type Country</option>
                        <option value="India">India</option>
                        <option value="USA">USA</option>
                        <option value="UK">UK</option>
                    </select>
                </div>
            </div>

            <br>

            <input type="text" name="pincode" class="form-control mb-3" placeholder="Pincode"
                   value="{{ old('pincode') }}">

            {{--  Business Contact Section --}}
            <h5 class="text-secondary mt-3">Business Contact Details</h5>
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="billing_spoc_name" class="form-control mb-2" placeholder="Billing SPOC Name"
                           value="{{ old('billing_spoc_name') }}">
                </div>
                <div class="col-md-4">
                    <input type="text" name="billing_spoc_contact" id="billing_spoc_contact" class="form-control mb-2" placeholder="Contact Number"
                           value="{{ old('billing_spoc_contact') }}">
                </div>
                <div class="col-md-4">
                    <input type="email" name="billing_spoc_email" id="billing_spoc_email" class="form-control mb-2" placeholder="Email"
                           value="{{ old('billing_spoc_email') }}">
                </div>
            </div>

            <input type="text" name="gstin" id="gstin" class="form-control mb-3" placeholder="GSTIN"
                   value="{{ old('gstin') }}">

            {{--  Invoice Details Section --}}
            <h5 class="text-secondary mt-3">Invoice Details</h5>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Invoice Email</label>
                    <input type="email" name="invoice_email" class="form-control"
                           value="{{ old('invoice_email') }}" placeholder="Enter invoice email">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Invoice CC</label>
                    <input type="email" name="invoice_cc" class="form-control"
                           value="{{ old('invoice_cc') }}" placeholder="Enter invoice CC email">
                </div>
            </div>

            {{--  Technical Support Section --}}
            <h5 class="text-secondary mt-3">Technical Support</h5>
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="support_spoc_name" class="form-control mb-2" placeholder="SPOC Name"
                           value="{{ old('support_spoc_name') }}">
                </div>
                <div class="col-md-4">
                    <input type="text" name="support_spoc_mobile" class="form-control mb-2" placeholder="Mobile Number"
                           value="{{ old('support_spoc_mobile') }}">
                </div>
                <div class="col-md-4">
                    <input type="email" name="support_spoc_email" class="form-control mb-2" placeholder="Email"
                           value="{{ old('support_spoc_email') }}">
                </div>
            </div>

            <!-- {{--  Status Dropdown --}}
            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div> -->
             {{--  Status Dropdown --}}
            <input type="hidden" name="status" value="Active">


            {{-- Form Buttons --}}
            <button type="submit" class="btn btn-success mt-3">Save Client</button> {{-- ✅ Save client data --}}
            <a href="{{ route('clients.index') }}" class="btn btn-secondary mt-3">Cancel</a> {{-- 🔙 Back to list --}}
        </form>
    </div>
</div>
<!-- GST API -->

<script>
const panStatus = document.getElementById('panStatus');
document.getElementById('pan_number').addEventListener('blur', function() {
    let pan = this.value.trim();
    panStatus.innerHTML = '';

    if (pan.length === 10) {
        panStatus.innerHTML = "⏳ Verifying PAN...";
        fetch(`/company/fetch/${pan}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('business_display_name').value = data.data.trade_name || '';
                    document.getElementById('billing_spoc_email').value = data.data.company_email || '';
                    document.getElementById('gstin').value = data.data.gstin || '';
                    document.getElementById('address1').value = data.data.address || '';
                    document.getElementById('billing_spoc_contact').value = data.data.company_phone || '';

                    panStatus.innerHTML = "✅ Company details auto-filled!";
                    panStatus.classList.remove("text-danger");
                    panStatus.classList.add("text-success");
                } else {
                    panStatus.innerHTML = "❌ No company found for this PAN.";
                    panStatus.classList.add("text-danger");
                    panStatus.classList.remove("text-success");
                }
            })
            .catch(err => {
                console.error('Error fetching company by PAN:', err);
                panStatus.innerHTML = "⚠️ Server error verifying PAN.";
                panStatus.classList.add("text-danger");
            });
    } else {
        panStatus.innerHTML = "⚠️ Enter valid 10-character PAN.";
        panStatus.classList.add("text-danger");
    }
});

</script>

@endsection
