@extends('layouts.app')



@section('content')

<div class="container-fluid py-4">

    <h4 class="text-primary fw-bold mb-3">Edit Feasibility</h4>



    <div class="card shadow border-0 p-4">



        {{-- ⚠️ Display validation errors if any --}}

        @if ($errors->any())

            <div class="alert alert-danger">

                <ul class="mb-0">

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li> {{-- Display each validation error --}}

                    @endforeach

                </ul>

            </div>

        @endif



        {{-- ✏️ Edit form starts --}}

        <form action="{{ route('feasibility.update', $feasibility->id) }}" method="POST">

            @csrf

            @method('PUT') {{-- Required for PUT request in Laravel --}}

            

            <div class="row g-3">



                {{-- Type of Service --}}

                <div class="col-md-4">

                    <label class="form-label fw-semibold">Type of Service *</label>

                    <select name="type_of_service" id="type_of_service_edit" class="form-select" required>

                        <option value="">Select</option>

                        <option {{ $feasibility->type_of_service == 'Broadband' ? 'selected' : '' }}>Broadband</option>

                        <option {{ $feasibility->type_of_service == 'ILL' ? 'selected' : '' }}>ILL</option>

                        <option {{ $feasibility->type_of_service == 'P2P' ? 'selected' : '' }}>P2P</option>

                    </select>

                </div>



                {{-- Company --}}

                <div class="col-md-4">

                    <label class="form-label fw-semibold">Company *</label>

                    <select name="company_id" id="company_id" class="form-select" required>

                        <option value="">Select Company</option>

                        @foreach($companies as $company)

                            <option value="{{ $company->id }}" {{ $company->id == $feasibility->company_id ? 'selected' : '' }}>

                                {{ $company->company_name }}

                            </option>

                        @endforeach

                    </select>

                </div>



                {{-- Client Name --}}

                <div class="col-md-4">

                    <label class="form-label fw-semibold">Client Name *</label>

                    <select name="client_id" id="client_id" class="form-select" required>

                        <option value="">Select Client</option>

                        @foreach($clients as $client)

                            <option value="{{ $client->id }}" {{ $client->id == $feasibility->client_id ? 'selected' : '' }}>

                                {{ $client->business_name ?: $client->client_name }}

                            </option>

                        @endforeach

                    </select>

                </div>



                {{-- Pincode --}}

                <div class="col-md-4">

                    <label class="form-label fw-semibold">Pincode *</label>

                    <input type="text" name="pincode" id="pincodeInput" value="{{ $feasibility->pincode }}" class="form-control" required>

                </div>



                {{-- State --}}

                <div class="col-md-4">

                    <label class="form-label fw-semibold">State *</label>

                    <select name="state" id="state" class="form-select select2-tags">

                        <option value="">Select or Type State</option>

                        <option value="Karnataka" {{ $feasibility->state == 'Karnataka' ? 'selected' : '' }}>Karnataka</option>

                        <option value="Tamil Nadu" {{ $feasibility->state == 'Tamil Nadu' ? 'selected' : '' }}>Tamil Nadu</option>

                        <option value="Telangana" {{ $feasibility->state == 'Telangana' ? 'selected' : '' }}>Telangana</option>

                    </select>

                </div>



                {{-- District --}}

                <div class="col-md-4">

                    <label class="form-label fw-semibold">District *</label>

                    <select name="district" id="district" class="form-select select2-tags">

                        <option value="">Select or Type District</option>

                        <option value="Salem" {{ $feasibility->district == 'Salem' ? 'selected' : '' }}>Salem</option>

                        <option value="Dharmapuri" {{ $feasibility->district == 'Dharmapuri' ? 'selected' : '' }}>Dharmapuri</option>

                        <option value="Erode" {{ $feasibility->district == 'Erode' ? 'selected' : '' }}>Erode</option>

                    </select>

                </div>



                {{-- Area --}}

                <div class="col-md-4">

                    <label class="form-label fw-semibold">Area *</label>

                    <select name="area" id="area" class="form-select select2-tags">

                        <option value="">Select or Type Area</option>

                        <option value="Uthagarai" {{ $feasibility->area == 'Uthagarai' ? 'selected' : '' }}>Uthagarai</option>

                        <option value="Harur" {{ $feasibility->area == 'Harur' ? 'selected' : '' }}>Harur</option>

                        <option value="Kottaiyur" {{ $feasibility->area == 'Kottaiyur' ? 'selected' : '' }}>Kottaiyur</option>

                    </select>

                </div>



                {{-- Address --}}

                <div class="col-md-6">

                    <label class="form-label fw-semibold">Address *</label>

                    <textarea name="address" class="form-control" rows="2" required>{{ $feasibility->address }}</textarea>

                </div>



                {{-- SPOC Name --}}

                <div class="col-md-3">

                    <label class="form-label fw-semibold">SPOC Name *</label>

                    <input type="text" name="spoc_name" value="{{ $feasibility->spoc_name }}" class="form-control" required>

                </div>



                {{-- SPOC Contact 1 --}}

                <div class="col-md-3">

                    <label class="form-label fw-semibold">SPOC Contact 1 *</label>

                    <input type="text" name="spoc_contact1" value="{{ $feasibility->spoc_contact1 }}" class="form-control" required>

                </div>



                {{-- SPOC Contact 2 --}}

                <div class="col-md-3">

                    <label class="form-label fw-semibold">SPOC Contact 2</label>

                    <input type="text" name="spoc_contact2" value="{{ $feasibility->spoc_contact2 }}" class="form-control">

                </div>



                {{-- SPOC Email --}}

                <div class="col-md-3">

                    <label class="form-label fw-semibold">SPOC Email</label>

                    <input type="email" name="spoc_email" value="{{ $feasibility->spoc_email }}" class="form-control">

                </div>



                {{-- No. of Links --}}

                <div class="col-md-3">

                    <label class="form-label fw-semibold">No. of Links *</label>

                    <select name="no_of_links" class="form-select" required>

                        <option value="">Select</option>

                        @for($i = 1; $i <= 4; $i++)

                            <option {{ $feasibility->no_of_links == $i ? 'selected' : '' }}>{{ $i }}</option>

                        @endfor

                    </select>

                </div>



                {{-- Vendor Type --}}

                <div class="col-md-3">

                    <label class="form-label fw-semibold">Vendor Type *</label>

                    <select name="vendor_type" class="form-select" required>

                        <option value="">Select</option>

                        <option {{ $feasibility->vendor_type == 'Same Vendor' ? 'selected' : '' }}>Same Vendor</option>

                        <option {{ $feasibility->vendor_type == 'Different Vendor' ? 'selected' : '' }}>Different Vendor</option>

                        <option {{ $feasibility->vendor_type == 'UBN' ? 'selected' : '' }}>UBN</option>

                        <option {{ $feasibility->vendor_type == 'UBS' ? 'selected' : '' }}>UBS</option>

                        <option {{ $feasibility->vendor_type == 'UBL' ? 'selected' : '' }}>UBL</option>

                        <option {{ $feasibility->vendor_type == 'INF' ? 'selected' : '' }}>INF</option>

                    </select>

                </div>



                {{-- Speed --}}

                <div class="col-md-3">

                    <label class="form-label fw-semibold">Speed *</label>

                    <input type="text" name="speed" value="{{ $feasibility->speed }}" placeholder="Mbps or Gbps" class="form-control" required>

                </div>



                {{-- Static IP --}}

                <div class="col-md-3">

                    <label class="form-label fw-semibold">Static IP *</label>

                    <select name="static_ip" id="static_ip_edit" class="form-select" required>

                        <option value="">Select</option>

                        <option value="Yes" {{ $feasibility->static_ip == 'Yes' ? 'selected' : '' }}>Yes</option>

                        <option value="No" {{ $feasibility->static_ip == 'No' ? 'selected' : '' }}>No</option>

                    </select>

                </div>

                {{-- Static IP Subnet --}}

                <div class="col-md-3">

                    <label class="form-label fw-semibold">Static IP Subnet</label>

                    <select name="static_ip_subnet" id="static_ip_subnet_edit" class="form-select" {{ $feasibility->static_ip != 'Yes' ? 'disabled' : '' }}>

                        <option value="">Select Subnet</option>

                        <option value="/32" {{ $feasibility->static_ip_subnet == '/32' ? 'selected' : '' }}>/32</option>

                        <option value="/31" {{ $feasibility->static_ip_subnet == '/31' ? 'selected' : '' }}>/31</option>

                        <option value="/30" {{ $feasibility->static_ip_subnet == '/30' ? 'selected' : '' }}>/30</option>

                        <option value="/29" {{ $feasibility->static_ip_subnet == '/29' ? 'selected' : '' }}>/29</option>

                        <option value="/28" {{ $feasibility->static_ip_subnet == '/28' ? 'selected' : '' }}>/28</option>

                        <option value="/27" {{ $feasibility->static_ip_subnet == '/27' ? 'selected' : '' }}>/27</option>

                        <option value="/26" {{ $feasibility->static_ip_subnet == '/26' ? 'selected' : '' }}>/26</option>

                        <option value="/25" {{ $feasibility->static_ip_subnet == '/25' ? 'selected' : '' }}>/25</option>

                        <option value="/24" {{ $feasibility->static_ip_subnet == '/24' ? 'selected' : '' }}>/24</option>

                    </select>

                    <small class="text-muted">Select subnet only if Static IP is Yes</small>

                </div>



                {{-- Expected Delivery --}}

                <div class="col-md-3">

                    <label class="form-label fw-semibold">Expected Delivery</label>

                    <input type="date" name="expected_delivery" value="{{ $feasibility->expected_delivery }}" class="form-control">

                </div>



                {{-- Expected Activation --}}

                <div class="col-md-3">

                    <label class="form-label fw-semibold">Expected Activation</label>

                    <input type="date" name="expected_activation" value="{{ $feasibility->expected_activation }}" class="form-control">

                </div>



                {{-- Hardware Required --}}

                <div class="col-md-3">

                    <label class="form-label fw-semibold">Hardware Required *</label>

                    <select name="hardware_required" id="hardware_required" class="form-select" required>

                        <option value="">Select</option>

                        <option value="1" {{ $feasibility->hardware_required == 1 ? 'selected' : '' }}>Yes</option>

                        <option value="0" {{ $feasibility->hardware_required == 0 ? 'selected' : '' }}>No</option>

                    </select>

                </div>



                {{-- Hardware Model Name (only visible when required) --}}

                <div class="col-md-3" id="hardware_name_div">

                    <label class="form-label fw-semibold">Hardware Model Name</label>

                    <input type="text" name="hardware_model_name" value="{{ $feasibility->hardware_model_name }}" class="form-control">

                </div>



                {{-- Status hidden (not editable here) --}}

                <input type="hidden" name="status" value="{{ $feasibility->status }}">



            </div>



            {{-- ✅ Submit & Cancel Buttons --}}

            <div class="mt-4 text-end">

                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Update</button>

                <a href="{{ route('feasibility.index') }}" class="btn btn-secondary">Cancel</a>

            </div>

        </form>

    </div>

</div>



{{-- ✅ Script Section --}}

<script>

document.getElementById('hardware_required').addEventListener('change', function() {

    // Show or hide hardware model name field dynamically

    document.getElementById('hardware_name_div').style.display = this.value == '1' ? 'block' : 'none';

});

// ✅ Static IP Subnet dropdown logic for edit form

document.addEventListener('DOMContentLoaded', function() {
    const staticIPSelect = document.getElementById('static_ip_edit');
    const subnetSelect = document.getElementById('static_ip_subnet_edit');
    
    staticIPSelect.addEventListener('change', function() {
        if (this.value === 'Yes') {
            subnetSelect.disabled = false;
            subnetSelect.required = true;
        } else {
            subnetSelect.disabled = true;
            subnetSelect.required = false;
            subnetSelect.value = '';
        }
    });
    
    // ✅ Auto-select Static IP = "Yes" when Type of Service = "ILL"
    const typeOfServiceSelect = document.getElementById('type_of_service_edit');
    
    typeOfServiceSelect.addEventListener('change', function() {
        if (this.value === 'ILL') {
            staticIPSelect.value = 'Yes';
            staticIPSelect.dispatchEvent(new Event('change')); // Trigger change to enable subnet
        }
    });
});





</script>



@endsection

