<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Helpers\TemplateHelper;
use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Gstin;
use App\Services\SurepassService;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
     $vendors = Vendor::orderBy('id', 'asc')->get();

        $vendors = Vendor::orderBy('id', 'asc')->get();
        // ✅ Use the helper correctly
        $permissions = TemplateHelper::getUserMenuPermissions('Vendor Master') ?? (object)[
    'can_add' => true,
    'can_edit' => true,
    'can_delete' => true,
    'can_view' => true,
];
        return view('vendors.index', compact('vendors', 'permissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('vendors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([
        'pan_number' => 'nullable|string|size:10',
       'vendor_name'          => 'required|string|max:255',
            'vendor_code'          => 'nullable|string|max:50|unique:vendor,vendor_code,' . ($vendor->id ?? 'null'),
            'business_display_name'=> 'nullable|string|max:255',
            'address1'             => 'nullable|string|max:255',
            'address2'             => 'nullable|string|max:255',
            'address3'             => 'nullable|string|max:255',
            'city'                 => 'nullable|string|max:100',
            'state'                => 'nullable|string|max:100',
            'country'              => 'nullable|string|max:100',
            'pincode'              => 'nullable|string|max:10',

            // Business Contact
            'contact_person_name'    => 'nullable|string|max:255',
            'contact_person_mobile' => 'nullable|string|max:20',
            'contact_person_email'   => 'nullable|email|max:255',
            'gstin'                => 'nullable|string|max:20',

            'pan_no'  => 'nullable|string|max:20',
            'bank_account_no'  => 'nullable|string|max:30',
            'ifsc_code'  =>  'nullable|string|max:30',

            'status'           => 'required|in:Active,Inactive',
        ]);

    // Auto-generate vendor_code
    $lastVendor = Vendor::latest('id')->first();
    $nextCode = 'V' . str_pad(($lastVendor->id ?? 0) + 1, 4, '0', STR_PAD_LEFT);

    Vendor::create([
        'vendor_name' => $request->vendor_name,
        'vendor_code' => $nextCode,
        'business_display_name' => $request->business_display_name,
        'address1' => $request->address1,
        'address2' => $request->address2,
        'address3' => $request->address3,
        'city' => $request->city,
        'state' => $request->state,
        'country' => $request->country,
        'pincode' => $request->pincode,
        'contact_person_name' => $request->contact_person_name,
        'contact_person_mobile' => $request->contact_person_mobile,
        'contact_person_email' => $request->contact_person_email,
        'gstin' => $request->gstin,
        'pan_no' => $request->pan_no,
        'bank_account_no' => $request->bank_account_no,
        'ifsc_code'  => $request->ifsc_code,
        'status' => $request->status,
        
    ]);

    return redirect()->route('vendors.index')->with('success', 'Vendor created successfully!');
}


    /**
     * Display the specified resource.
     */
    public function view($id)
{
    $vendor = \App\Models\Vendor::findOrFail($id);
    return view('vendors.view', compact('vendor'));
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vendor $vendor)
    {
        return view('vendors.edit', compact('vendor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vendor $vendor)
    {
        $validated =  $request->validate([
        'pan_number' => 'nullable|string|size:10',
       'vendor_name'          => 'required|string|max:255',
            'vendor_code'          => 'nullable|string|max:50|unique:vendor,vendor_code,' . ($vendor->id ?? 'null'),
            'business_display_name'=> 'nullable|string|max:255',
            'address1'             => 'nullable|string|max:255',
            'address2'             => 'nullable|string|max:255',
            'address3'             => 'nullable|string|max:255',
            'city'                 => 'nullable|string|max:100',
            'state'                => 'nullable|string|max:100',
            'country'              => 'nullable|string|max:100',
            'pincode'              => 'nullable|string|max:10',

            // Business Contact
            'contact_person_name'    => 'nullable|string|max:255',
            'contact_person_mobile' => 'nullable|string|max:20',
            'contact_person_email'   => 'nullable|email|max:255',
            'gstin'                => 'nullable|string|max:20',

            'pan_no'  => 'nullable|string|max:20',
            'bank_account_no'  => 'nullable|string|max:30',
            'ifsc_code'  =>  'nullable|string|max:30',

            'status'           => 'required|in:Active,Inactive',
        ]);


        $vendor->update($validated);

        return redirect()->route('vendors.index')->with('success', 'Vendor updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vendor $vendor)
    {
        $vendor->delete();
        return redirect()->route('vendors.index')->with('success', 'Vendor deleted successfully.');
    }
    /**
     * 
     */
     public function toggleStatus($id)
{
    $vendor = Vendor::findOrFail($id);

    // Toggle Active/Inactive
    $vendor->status = $vendor->status === 'Active' ? 'Inactive' : 'Active';
    $vendor->save();

    return redirect()->route('vendors.index')
                     ->with('success', 'Vendor status updated successfully.');
}
// PAN Verification
public function verifyPan(Request $request)
{
    $company = Company::where('pan_no', $request->pan)->first();
    if ($company) {
        return response()->json(['success' => true, 'data' => $company]);
    } else {
        return response()->json(['success' => false, 'message' => 'PAN not found']);
    }
}
// Fetch GST Details
public function fetchGST($pan, $state)
{
    $pan = strtoupper($pan);
    if (strlen($pan) !== 10) {
        return response()->json(['success' => false, 'message' => 'Invalid PAN']);
    }

    // Step 1: Generate GSTIN without checksum
    $partialGSTIN = $state . $pan . "1Z";

    // Step 2: Generate checksum
    $checksum = $this->getGSTChecksum($partialGSTIN);

    // Final GSTIN
    $gstin = $partialGSTIN . $checksum;

    // Step 3: Call GST API
    $url = "https://sheet.gstincheck.co.in/check/{$gstin}";
    $response = Http::timeout(10)->get($url);

    if ($response->failed() || !isset($response['tradeNam'])) {
        return response()->json(['success' => false]);
    }

    $data = [
        'gstin' => $gstin,
        'trade_name' => $response['tradeNam'],
        'address' => $response['pradr']['addr']['bno']
                    . ", " . $response['pradr']['addr']['st']
                    . ", " . $response['pradr']['addr']['dst'],
        'company_email' => $response['pradr']['email'] ?? '',
        'company_phone' => $response['pradr']['phone'] ?? '',
    ];

    return response()->json(['success' => true, 'data' => $data]);
}

/* Generate GSTIN Checksum */
private function getGSTChecksum($input)
{
    $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $factor = 1;
    $sum = 0;

    for ($i = 0; $i < strlen($input); $i++) {
        $codePoint = strpos($chars, $input[$i]);
        $digit = $factor * $codePoint;

        $factor = ($factor == 1) ? 2 : 1;

        $digit = floor($digit / 36) + ($digit % 36);
        $sum += $digit;
    }

    $checksumPoint = (36 - ($sum % 36)) % 36;
    return $chars[$checksumPoint];
}

/**
 * Fetch GSTIN by PAN using Surepass API
 */
public function fetchGstinByPan(Request $request)
{
    $request->validate([
        'pan_number' => 'required|string|size:10',
        'vendor_id' => 'nullable|integer'
    ]);

    $surepassService = new SurepassService();
    $result = $surepassService->getGstinByPan($request->pan_number);

    if (!$result['success']) {
        return response()->json($result);
    }

    // Parse the GSTIN data
    $gstinList = $surepassService->parseGstinData($result['data']);

    if (empty($gstinList)) {
        return response()->json([
            'success' => false,
            'message' => 'No GSTIN found for this PAN'
        ]);
    }

    // Don't save automatically - let user select which GSTINs to save
    return response()->json([
        'success' => true,
        'data' => $gstinList,
        'message' => 'GSTIN details fetched successfully'
    ]);
}

/**
 * Save selected GSTINs for a vendor
 */
public function saveSelectedGstins(Request $request)
{
    $request->validate([
        'vendor_id' => 'required|integer|exists:vendors,id',
        'gstins' => 'required|array|min:1',
        'gstins.*.gstin' => 'required|string|size:15',
        'gstins.*.trade_name' => 'nullable|string',
        'gstins.*.legal_name' => 'nullable|string',
        'gstins.*.principal_business_address' => 'nullable|string',
        'gstins.*.building_name' => 'nullable|string',
        'gstins.*.building_number' => 'nullable|string',
        'gstins.*.floor_number' => 'nullable|string',
        'gstins.*.street' => 'nullable|string',
        'gstins.*.location' => 'nullable|string',
        'gstins.*.district' => 'nullable|string',
        'gstins.*.city' => 'nullable|string',
        'gstins.*.state' => 'nullable|string',
        'gstins.*.state_code' => 'nullable|string|max:2',
        'gstins.*.pincode' => 'nullable|string|max:10',
        'gstins.*.is_primary' => 'nullable|boolean',
    ]);

    try {
        // If a GSTIN is marked as primary, unmark all others
        $hasPrimary = collect($request->gstins)->contains('is_primary', true);
        
        if ($hasPrimary) {
            Gstin::where('entity_type', 'vendor')
                ->where('entity_id', $request->vendor_id)
                ->update(['is_primary' => false]);
        }

        $conflicts = [];
        $savedCount = 0;

        // Save each selected GSTIN with duplicate checks across entities
        foreach ($request->gstins as $gstinData) {
            $existingOther = Gstin::where('gstin', $gstinData['gstin'])
                ->where(function($q) use ($request) {
                    $q->where('entity_type', '!=', 'vendor')
                      ->orWhere('entity_id', '!=', $request->vendor_id);
                })
                ->first();

            if ($existingOther) {
                $conflicts[] = $gstinData['gstin'];
                continue; // skip saving this GSTIN
            }

            Gstin::updateOrCreate(
                [
                    'entity_type' => 'vendor',
                    'entity_id' => $request->vendor_id,
                    'gstin' => $gstinData['gstin']
                ],
                [
                    'trade_name' => $gstinData['trade_name'] ?? null,
                    'legal_name' => $gstinData['legal_name'] ?? null,
                    'principal_business_address' => $gstinData['principal_business_address'] ?? null,
                    'building_name' => $gstinData['building_name'] ?? null,
                    'building_number' => $gstinData['building_number'] ?? null,
                    'floor_number' => $gstinData['floor_number'] ?? null,
                    'street' => $gstinData['street'] ?? null,
                    'location' => $gstinData['location'] ?? null,
                    'district' => $gstinData['district'] ?? null,
                    'city' => $gstinData['city'] ?? null,
                    'state' => $gstinData['state'] ?? null,
                    'state_code' => $gstinData['state_code'] ?? null,
                    'pincode' => $gstinData['pincode'] ?? null,
                    'status' => 'Active',
                    'is_primary' => $gstinData['is_primary'] ?? false,
                ]
            );
            $savedCount++;
        }

        $message = $savedCount . ' GSTIN(s) saved successfully';
        if (!empty($conflicts)) {
            $message .= '. Skipped duplicates already linked to other entities: ' . implode(', ', $conflicts);
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error saving GSTINs: ' . $e->getMessage()
        ], 500);
    }
}

}


