@extends('layouts.app')



@section('content')

<div class="container-fluid py-4">

    <h4 class="text-primary fw-bold mb-3">Add Feasibility</h4>



    <div class="card shadow border-0 p-4">



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



         {{-- Form starts here --}}

        <form action="{{ route('feasibility.store') }}" method="POST">

            @csrf

            <div class="row g-3">



                <div class="col-md-4">

                    <label class="form-label fw-semibold">Feasibility Request ID</label>

                    <input type="text" class="form-control bg-light" value="Auto-generated" readonly>

                    <small class="text-muted">ID will be generated automatically when saved</small>

                </div>



                <div class="col-md-4">

                    <label class="form-label fw-semibold">Type of Service *</label>

                    <select name="type_of_service" class="form-select" required>

                        <option value="">Select</option>

                        <option>Broadband</option>

                        <option>ILL</option>

                        <option>P2P</option>

                    </select>

                </div>



                <div class="col-md-4">

                    <label class="form-label fw-semibold">Company *</label>

                    <select name="company_id" id="company_id" class="form-select" required>

                        <option value="">Select Company</option>

                        @foreach($companies as $company)

                            <option value="{{ $company->id }}">{{ $company->company_name }}</option>

                        @endforeach

                    </select>

                </div>



                <div class="col-md-4">

                    <label class="form-label fw-semibold">Client Name *</label>

                    <select name="client_id" id="client_id" class="form-select" required>

                        <option value="">Select Client</option>

                        @foreach($clients as $client)

                            <option value="{{ $client->id }}">{{ $client->business_name ?: $client->client_name }}</option>

                        @endforeach

                    </select>

                </div>





                <div class="col-md-4">

                    <label class="form-label fw-semibold">Pincode *</label>

                    <input type="text" name="pincode" id="pincode" maxlength="6" class="form-control" required>

           <!-- <button type="button" id="pincodeVerifyBtn" class="btn btn-primary">Verify</button> -->

                </div>



                <div class="col-md-4">

                    <label class="form-label fw-semibold">State *</label>

                    <select name="state" id="state" class="form-select select2-tags">

                        <option value="">Select or Type State</option>

                        <option value="Karnataka">Karnataka</option>

                        <option value="Tamil Nadu">Tamil Nadu</option>

                        <option value="Telangana">Telangana</option>

                    </select>

                </div>



                <div class="col-md-4">

                    <label class="form-label fw-semibold">District *</label>

                   <select name="district" id="district" class="form-select select2-tags">

                        <option value="">Select or Type District</option>

                        <option value="Salem">Salem</option>

                        <option value="Dharmapuri">Dharmapuri</option>

                        <option value="Erode">Erode</option>

                    </select>

                </div>



                <div class="col-md-4">

                    <label class="form-label fw-semibold">Area *</label>

                    <select name="area" id="post_office" class="form-select select2-tags">

                        <option value="">Select or Type Area</option>

                        <option value="Uthagarai">Uthagarai</option>

                        <option value="Harur">Harur</option>

                        <option value="Kottaiyur">Kottaiyur</option>

                    </select>

                </div>



                <div class="col-md-6">

                    <label class="form-label fw-semibold">Address *</label>

                    <textarea name="address" class="form-control" rows="2" required></textarea>

                </div>



                <div class="col-md-3">

                    <label class="form-label fw-semibold">SPOC Name *</label>

                    <input type="text" name="spoc_name" class="form-control" required>

                </div>



                <div class="col-md-3">

                    <label class="form-label fw-semibold">SPOC Contact 1 *</label>

                    <input type="text" name="spoc_contact1" class="form-control" required>

                </div>



                <div class="col-md-3">

                    <label class="form-label fw-semibold">SPOC Contact 2</label>

                    <input type="text" name="spoc_contact2" class="form-control">

                </div>



                <div class="col-md-3">

                    <label class="form-label fw-semibold">SPOC Email</label>

                    <input type="email" name="spoc_email" class="form-control">

                </div>



                <div class="col-md-3">

                    <label class="form-label fw-semibold">No. of Links *</label>

                    <select name="no_of_links" class="form-select" required>

                        <option value="">Select</option>

                        <option>1</option>

                        <option>2</option>

                        <option>3</option>

                        <option>4</option>

                    </select>

                </div>



                <div class="col-md-3">

                    <label class="form-label fw-semibold">Vendor Type *</label>

                    <select name="vendor_type" class="form-select" required>

                        <option value="">Select</option>

                        <option>Same Vendor</option>

                        <option>Different Vendor</option>

                    </select>

                </div>



                <div class="col-md-3">

                    <label class="form-label fw-semibold">Speed *</label>

                    <input type="text" name="speed" placeholder="Mbps or Gbps" class="form-control" required>

                </div>



                <div class="col-md-3">

                    <label class="form-label fw-semibold">Static IP *</label>

                    <select name="static_ip" class="form-select" required>

                        <option value="">Select</option>

                        <option value="Yes">Yes</option>

                        <option value="No">No</option>

                    </select>

                </div>





                <div class="col-md-3">

                    <label class="form-label fw-semibold">Expected Delivery</label>

                    <input type="date" name="expected_delivery" class="form-control">

                </div>



                <div class="col-md-3">

                    <label class="form-label fw-semibold">Expected Activation</label>

                    <input type="date" name="expected_activation" class="form-control">

                </div>



                <div class="col-md-3">

                    <label class="form-label fw-semibold">Hardware Required *</label>

                    <select name="hardware_required" id="hardware_required" class="form-select" required>

                        <option value="">Select</option>

                        <option value="1">Yes</option>

                        <option value="0">No</option>

                    </select>

                </div>



                <div class="col-md-3" id="hardware_name_div" style="display:none;">

                    <label class="form-label fw-semibold">Hardware Model Name</label>

                    <input type="text" name="hardware_model_name" class="form-control">

                </div>



                    {{--  Status Dropdown --}}

            <input type="hidden" name="status" value="Active">





            </div>



            <div class="mt-4 text-end">

                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>

                <!-- <a href="{{ route('feasibility.index') }}" class="btn btn-secondary">Cancel</a> -->

            </div>

        </form>

    </div>

</div>



<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>



<script>

document.getElementById('hardware_required').addEventListener('change', function() {

    document.getElementById('hardware_name_div').style.display = this.value == '1' ? 'block' : 'none';

});



// Helper function to set value in select dropdown, creating option if needed

function setSelectValue(selectElement, value) {

  console.log('setSelectValue called with:', selectElement.id, 'value:', value);

  

  if (!value || value === '') {

    selectElement.value = '';

    // If it's a Select2 element, trigger change

    if (typeof $ !== 'undefined' && typeof $(selectElement).select2 === 'function') {

      $(selectElement).val('').trigger('change');

    }

    return;

  }

  

  // Check if option already exists

  let optionExists = false;

  for (let option of selectElement.options) {

    if (option.value === value) {

      optionExists = true;

      break;

    }

  }

  

  // If option doesn't exist, create it

  if (!optionExists) {

    const newOption = document.createElement('option');

    newOption.value = value;

    newOption.text = value;

    selectElement.appendChild(newOption);

    console.log('Created new option:', value, 'for', selectElement.id);

  }

  

  // Set the value

  selectElement.value = value;

  console.log('Set native value for', selectElement.id, 'to:', value);

  

  // Handle Select2 if available

  if (typeof $ !== 'undefined') {

    try {

      const $element = $(selectElement);

      // Check if Select2 is initialized

      if (typeof $element.select2 === 'function' && $element.hasClass('select2-hidden-accessible')) {

        $element.val(value).trigger('change');

        console.log('Triggered Select2 change for', selectElement.id);

      }

    } catch (error) {

      console.log('Select2 not available or error:', error);

    }

  }

  

  console.log('Final value for', selectElement.id, ':', selectElement.value);

}



// Pincode lookup function

function lookupPincode() {

  const pincodeField = document.getElementById('pincode');

  const p = pincodeField.value.trim();

  

  // Only proceed if we have exactly 6 digits

  if (!/^\d{6}$/.test(p)) return;

  

  // Get field references

  const stateField = document.getElementById('state');

  const districtField = document.getElementById('district');

  const areaField = document.getElementById('post_office');

  

  // Store original values in case of error

  const originalState = stateField.value;

  const originalDistrict = districtField.value;

  const originalArea = areaField.value;

  

  // Show loading state

  setSelectValue(stateField, 'Loading...');

  setSelectValue(districtField, 'Loading...');

  setSelectValue(areaField, 'Loading...');

  

  console.log('🔍 Looking up pincode:', p);

  

  // Make API call

  axios.post('/api/pincode/lookup', { pincode: p })

    .then(r => {

      const d = r.data;

      console.log('✅ Pincode lookup successful:', d);

      console.log('State field element:', stateField);

      console.log('District field element:', districtField);

      console.log('Area field element:', areaField);

      

      // Update fields with fetched data

      console.log('Setting state to:', d.state);

      setSelectValue(stateField, d.state || '');

      

      console.log('Setting district to:', d.district);

      setSelectValue(districtField, d.district || '');

      

      console.log('Setting area to:', d.post_office);

      setSelectValue(areaField, d.post_office || '');

      

      // Show success message briefly

      const notification = document.createElement('div');

      notification.style.cssText = `

        position: fixed; top: 20px; right: 20px; 

        background: #d4edda; color: #155724; 

        padding: 10px 15px; border-radius: 5px; 

        border: 1px solid #c3e6cb; z-index: 9999;

        font-size: 14px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);

      `;

      notification.innerHTML = `✅ Location found: ${d.state}, ${d.district}`;

      document.body.appendChild(notification);

      

      // Remove notification after 3 seconds

      setTimeout(() => {

        if (notification.parentNode) {

          notification.parentNode.removeChild(notification);

        }

      }, 3000);

    })

    .catch(err => {

      console.error('❌ Pincode lookup failed:', err);

      

      // Restore original values

      setSelectValue(stateField, originalState);

      setSelectValue(districtField, originalDistrict);

      setSelectValue(areaField, originalArea);

      

      // Show error message

      let errorMessage = 'Unable to fetch pincode details. Please try again or enter manually.';

      if (err.response && err.response.status === 404) {

        errorMessage = 'Pincode not found. Please check the pincode and try again.';

      } else if (err.response && err.response.status === 422) {

        errorMessage = 'Invalid pincode format. Please enter a 6-digit pincode.';

      }

      

      // Show error notification

      const errorNotification = document.createElement('div');

      errorNotification.style.cssText = `

        position: fixed; top: 20px; right: 20px; 

        background: #f8d7da; color: #721c24; 

        padding: 10px 15px; border-radius: 5px; 

        border: 1px solid #f5c6cb; z-index: 9999;

        font-size: 14px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);

      `;

      errorNotification.innerHTML = `❌ ${errorMessage}`;

      document.body.appendChild(errorNotification);

      

      // Remove error notification after 5 seconds

      setTimeout(() => {

        if (errorNotification.parentNode) {

          errorNotification.parentNode.removeChild(errorNotification);

        }

      }, 5000);

    });

}



// Add multiple event listeners for better responsiveness

const pincodeInput = document.getElementById('pincode');



// Trigger on blur (when user clicks outside the field)

pincodeInput.addEventListener('blur', lookupPincode);



// Trigger on Enter key press

pincodeInput.addEventListener('keypress', function(e) {

  if (e.key === 'Enter') {

    e.preventDefault(); // Prevent form submission

    lookupPincode();

  }

});



// Trigger on input with debouncing (wait for user to stop typing)

let pincodeTimeout;

pincodeInput.addEventListener('input', function() {

  // Clear previous timeout

  if (pincodeTimeout) {

    clearTimeout(pincodeTimeout);

  }

  

  // Set new timeout to trigger after 1 second of no typing

  pincodeTimeout = setTimeout(() => {

    const value = this.value.trim();

    if (/^\d{6}$/.test(value)) {

      lookupPincode();

    }

  }, 1000);

});



// Vendor Type Logic based on No. of Links

document.querySelector('select[name="no_of_links"]').addEventListener('change', function() {

    const vendorTypeSelect = document.querySelector('select[name="vendor_type"]');

    const noOfLinks = parseInt(this.value);

    

    // Clear current selection

    vendorTypeSelect.selectedIndex = 0;

    

    if (noOfLinks === 1) {

    vendorTypeSelect.value = 'Same Vendor';

    vendorTypeSelect.readOnly = true;

    vendorTypeSelect.style.pointerEvents = "none";

    vendorTypeSelect.style.background = "#e9ecef";

} else if (noOfLinks > 1) {

    vendorTypeSelect.value = 'Different Vendor';

    vendorTypeSelect.readOnly = true;

    vendorTypeSelect.style.pointerEvents = "none";

    vendorTypeSelect.style.background = "#e9ecef";

} else {

    vendorTypeSelect.readOnly = false;

    vendorTypeSelect.style.pointerEvents = "auto";

    vendorTypeSelect.style.background = "";

}



});



// Initialize the vendor type field state on page load

document.addEventListener('DOMContentLoaded', function() {

    const noOfLinksSelect = document.querySelector('select[name="no_of_links"]');

    const vendorTypeSelect = document.querySelector('select[name="vendor_type"]');

    

    // If there's already a value selected, trigger the change event

    if (noOfLinksSelect.value) {

        noOfLinksSelect.dispatchEvent(new Event('change'));

    }

});





</script>

@endsection

