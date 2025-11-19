<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\Feasibility;
use App\Models\FeasibilityStatus;
use App\Models\Deliverables;
use App\Helpers\TemplateHelper;
use Illuminate\Support\Facades\Log;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with('feasibility.client')->orderBy('created_at', 'desc')->get();
        $permissions = TemplateHelper::getUserMenuPermissions('User Type') ?? (object)[
    'can_add' => true,
    'can_edit' => true,
    'can_delete' => true,
    'can_view' => true,
        ];
        return view('sm.purchaseorder.index', compact('purchaseOrders', 'permissions'));
    }

    public function create()
    {
        // Get only closed feasibilities that don't have a Purchase Order yet
        $usedFeasibilityIds = PurchaseOrder::pluck('feasibility_id')->toArray();
        
        $closedFeasibilities = FeasibilityStatus::with('feasibility.client')
            ->where('status', 'Closed')
            ->whereNotIn('feasibility_id', $usedFeasibilityIds)
            ->get();
        
        return view('sm.purchaseorder.create', compact('closedFeasibilities'));
    }

    public function store(Request $request)
    {
        // Basic validation for main fields
        $rules = [
            'feasibility_id' => 'required|exists:feasibilities,id',
            'po_number' => 'required|string|max:255|unique:purchase_orders,po_number',
            'po_date' => 'required|date',
            'no_of_links' => 'required|integer|min:1|max:4',
            'contract_period' => 'required|integer|min:1',
        ];
        
        // ✅ Check if type_of_service is ILL - make static IP mandatory
        $feasibility = Feasibility::find($request->feasibility_id);
        $isILL = $feasibility && $feasibility->type_of_service === 'ILL';
        
        // Dynamic validation for pricing fields based on number of links
        $noOfLinks = $request->input('no_of_links');
        for ($i = 1; $i <= $noOfLinks; $i++) {
            $rules["arc_link_{$i}"] = 'required|numeric|min:0';
            $rules["otc_link_{$i}"] = 'required|numeric|min:0';
            
            // ✅ Static IP is required for ILL connections
            if ($isILL) {
                $rules["static_ip_link_{$i}"] = 'required|numeric|min:0.01';
            } else {
                $rules["static_ip_link_{$i}"] = 'required|numeric|min:0';
            }
        }

        $validated = $request->validate($rules);
        
        // Get feasibility vendor minimum values
        $feasibilityStatus = FeasibilityStatus::where('feasibility_id', $validated['feasibility_id'])->first();

        // Calculate totals from individual link pricing
        $totalARC = 0;
        $totalOTC = 0;
        $totalStaticIP = 0;
for ($i = 1; $i <= $noOfLinks; $i++) {
    $totalARC += (float)$request->input("arc_link_{$i}");
    $totalOTC += (float)$request->input("otc_link_{$i}");
    $totalStaticIP += (float)$request->input("static_ip_link_{$i}");
}

        // ✅ Server-side validation: Check exact match and 20% minimum requirement
        $error = $this->validatePricing($feasibilityStatus, $request, $noOfLinks);

        if ($error) {
            return back()->withInput()->with('error', $error);
        }
        
        // Prepare data for storage
        $poData = [
            'feasibility_id' => $validated['feasibility_id'],
            'po_number' => $validated['po_number'],
            'po_date' => $validated['po_date'],
            'no_of_links' => $validated['no_of_links'],
            'contract_period' => $validated['contract_period'],
            'arc_per_link' => $totalARC / $noOfLinks, // Average per link (backward compatibility)
            'otc_per_link' => $totalOTC / $noOfLinks, // Average per link (backward compatibility)
            'static_ip_cost_per_link' => $totalStaticIP / $noOfLinks, // Average per link (backward compatibility)
            'status' => 'Active'
        ];
        
        // Add individual link data to support multi-vendor validation
        for ($i = 1; $i <= $noOfLinks; $i++) {
            $poData["arc_link_{$i}"] = $request->input("arc_link_{$i}");
            $poData["otc_link_{$i}"] = $request->input("otc_link_{$i}");
            $poData["static_ip_link_{$i}"] = $request->input("static_ip_link_{$i}");
        }

        $purchaseOrder = PurchaseOrder::create($poData);

        // 🚀 AUTO-CREATE DELIVERABLE when Purchase Order is created
        $this->createDeliverableFromPurchaseOrder($purchaseOrder);
        

        return redirect()->route('sm.purchaseorder.index')
            ->with('success', 'Purchase Order created successfully and deliverable generated!');
    }

    public function show($id)
    {
        $purchaseOrder = PurchaseOrder::with('feasibility.client')->findOrFail($id);
        return view('sm.purchaseorder.view', compact('purchaseOrder'));
    }

    public function edit($id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        
        // Get closed feasibilities excluding those already used, but include current PO's feasibility
        $usedFeasibilityIds = PurchaseOrder::where('id', '!=', $id)->pluck('feasibility_id')->toArray();
        
        $closedFeasibilities = FeasibilityStatus::with('feasibility.client')
            ->where('status', 'Closed')
            ->whereNotIn('feasibility_id', $usedFeasibilityIds)
            ->get();
        
        return view('sm.purchaseorder.edit', compact('purchaseOrder', 'closedFeasibilities'));
    }

    public function update(Request $request, $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        
        // Base validation
        $validated = $request->validate([
            'feasibility_id' => 'required|exists:feasibilities,id',
            'po_number' => 'required|string|max:255|unique:purchase_orders,po_number,' . $id,
            'po_date' => 'required|date',
            'no_of_links' => 'required|integer|min:1|max:4',
            'contract_period' => 'required|integer|min:1',
        ]);

        $noOfLinks = $validated['no_of_links'];
        
        // ✅ Check if type_of_service is ILL - make static IP mandatory
        $feasibility = Feasibility::find($request->feasibility_id);
        $isILL = $feasibility && $feasibility->type_of_service === 'ILL';
        
        // Dynamic validation for link pricing
        $linkValidationRules = [];
        for ($i = 1; $i <= $noOfLinks; $i++) {
            $linkValidationRules["arc_link_{$i}"] = 'required|numeric|min:0';
            $linkValidationRules["otc_link_{$i}"] = 'required|numeric|min:0';
            
            // ✅ Static IP is required for ILL connections
            if ($isILL) {
                $linkValidationRules["static_ip_link_{$i}"] = 'required|numeric|min:0.01';
            } else {
                $linkValidationRules["static_ip_link_{$i}"] = 'required|numeric|min:0';
            }
        }
        
        $request->validate($linkValidationRules);
        
        // Get feasibility vendor minimum values
        $feasibilityStatus = FeasibilityStatus::where('feasibility_id', $validated['feasibility_id'])->first();

        // Calculate totals from individual link pricing
        $totalARC = 0;
        $totalOTC = 0;
        $totalStaticIP = 0;
for ($i = 1; $i <= $noOfLinks; $i++) {
    $totalARC += (float)$request->input("arc_link_{$i}");
    $totalOTC += (float)$request->input("otc_link_{$i}");
    $totalStaticIP += (float)$request->input("static_ip_link_{$i}");
}

        // ✅ Server-side validation: Check exact match and 20% minimum requirement
        $error = $this->validatePricing($feasibilityStatus, $request, $noOfLinks);

        if ($error) {
            return back()->withInput()->with('error', $error);
        }
        
        // Prepare data for update
        $poData = [
            'feasibility_id' => $validated['feasibility_id'],
            'po_number' => $validated['po_number'],
            'po_date' => $validated['po_date'],
            'no_of_links' => $validated['no_of_links'],
            'contract_period' => $validated['contract_period'],
            'arc_per_link' => $totalARC / $noOfLinks, // Average per link (backward compatibility)
            'otc_per_link' => $totalOTC / $noOfLinks, // Average per link (backward compatibility)
            'static_ip_cost_per_link' => $totalStaticIP / $noOfLinks, // Average per link (backward compatibility)
        ];
        
        // Add individual link data
        for ($i = 1; $i <= $noOfLinks; $i++) {
            $poData["arc_link_{$i}"] = $request->input("arc_link_{$i}");
            $poData["otc_link_{$i}"] = $request->input("otc_link_{$i}");
            $poData["static_ip_link_{$i}"] = $request->input("static_ip_link_{$i}");
        }
        
        // Clear unused link fields
        for ($i = $noOfLinks + 1; $i <= 4; $i++) {
            $poData["arc_link_{$i}"] = null;
            $poData["otc_link_{$i}"] = null;
            $poData["static_ip_link_{$i}"] = null;
        }

        // $purchaseOrder->update($poData);
        $oldStatus = $purchaseOrder->status;

// Update data
$purchaseOrder->update($poData);

// If PO is moved to Closed → create Deliverable
if ($oldStatus !== 'Closed' && $request->status === 'Closed') {
    $this->createDeliverableFromPurchaseOrder($purchaseOrder);
}


        return redirect()->route('sm.purchaseorder.index')
            ->with('success', 'Purchase Order updated successfully!');
    }

    public function toggleStatus($id)
{
    $purchaseOrder = PurchaseOrder::findOrFail($id);

    // Toggle Active/Inactive
    $purchaseOrder->status = $purchaseOrder->status === 'Active' ? 'Inactive' : 'Active';
    $purchaseOrder->save();

    return redirect()->route('sm.purchaseorder.index')
                     ->with('success', 'Purchase Order status updated successfully.');
}

    public function destroy($id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        $purchaseOrder->delete();
        
        return redirect()->route('sm.purchaseorder.index')
            ->with('success', 'Purchase Order deleted successfully!');
    }

    // AJAX method to get feasibility details
    public function getFeasibilityDetails($id)
    {
        $feasibility = Feasibility::with(['client', 'feasibilityStatus'])->findOrFail($id);
        
        // Get vendor pricing from feasibility status
        $feasibilityStatus = $feasibility->feasibilityStatus;
        
        // For now, use vendor1 pricing (you can modify this logic to select specific vendor)
        $pricing = [
            'arc_per_link' => $feasibilityStatus->vendor1_arc ?? 0,
            'otc_per_link' => $feasibilityStatus->vendor1_otc ?? 0,
            'static_ip_cost_per_link' => $feasibilityStatus->vendor1_static_ip_cost ?? 0
        ];
        
        // Build vendor pricing array for multi-vendor validation
        $vendorPricing = [];
        
        // Add vendor1 if it has data
        if ($feasibilityStatus->vendor1_name) {
            $vendorPricing['vendor1'] = [
                'name' => $feasibilityStatus->vendor1_name,
                'arc' => (float) ($feasibilityStatus->vendor1_arc ?? 0),
                'otc' => (float) ($feasibilityStatus->vendor1_otc ?? 0),
                'static_ip_cost' => (float) ($feasibilityStatus->vendor1_static_ip_cost ?? 0)
            ];
        }
        
        // Add vendor2 if it has data
        if ($feasibilityStatus->vendor2_name) {
            $vendorPricing['vendor2'] = [
                'name' => $feasibilityStatus->vendor2_name,
                'arc' => (float) ($feasibilityStatus->vendor2_arc ?? 0),
                'otc' => (float) ($feasibilityStatus->vendor2_otc ?? 0),
                'static_ip_cost' => (float) ($feasibilityStatus->vendor2_static_ip_cost ?? 0)
            ];
        }
        
        // Add vendor3 if it has data
        if ($feasibilityStatus->vendor3_name) {
            $vendorPricing['vendor3'] = [
                'name' => $feasibilityStatus->vendor3_name,
                'arc' => (float) ($feasibilityStatus->vendor3_arc ?? 0),
                'otc' => (float) ($feasibilityStatus->vendor3_otc ?? 0),
                'static_ip_cost' => (float) ($feasibilityStatus->vendor3_static_ip_cost ?? 0)
            ];
        }
        
        // Add vendor4 if it has data
        if ($feasibilityStatus->vendor4_name) {
            $vendorPricing['vendor4'] = [
                'name' => $feasibilityStatus->vendor4_name,
                'arc' => (float) ($feasibilityStatus->vendor4_arc ?? 0),
                'otc' => (float) ($feasibilityStatus->vendor4_otc ?? 0),
                'static_ip_cost' => (float) ($feasibilityStatus->vendor4_static_ip_cost ?? 0)
            ];
        }

        return response()->json([
            'client' => $feasibility->client,
            'feasibility' => $feasibility,
            'no_of_links' => $feasibility->no_of_links,
            'arc_per_link' => (float) $pricing['arc_per_link'],
            'otc_per_link' => (float) $pricing['otc_per_link'],
            'static_ip_cost_per_link' => (float) $pricing['static_ip_cost_per_link'],
            'vendor_pricing' => $vendorPricing
        ]);
    }

    /**
     * Create deliverable from purchase order
     */
    private function createDeliverableFromPurchaseOrder($purchaseOrder)
    {
        try {
            // Check if deliverable already exists for this SPECIFIC purchase order
            $existingDeliverable = Deliverables::where('purchase_order_id', $purchaseOrder->id)->first();
            
            if ($existingDeliverable) {
                Log::info("Deliverable already exists for Purchase Order ID: {$purchaseOrder->id}");
                return;
            }
            
            // Get feasibility and feasibility status data
            $feasibility = $purchaseOrder->feasibility;
            $feasibilityStatus = FeasibilityStatus::where('feasibility_id', $purchaseOrder->feasibility_id)->first();
            
            if (!$feasibility) {
                Log::error("Feasibility not found for Purchase Order ID: {$purchaseOrder->id}");
                return;
            }
            
            // Get client for GST number
            $client = $feasibility->client;
            
            // Create deliverable with data from feasibility and purchase order
            $deliverable = Deliverables::create([
                'feasibility_id' => $feasibility->id,
                'purchase_order_id' => $purchaseOrder->id,
                'status' => 'Open',
                
                // ✅ Site Information from Feasibility table
                'site_address' => $feasibility->address ?? '',           // feasibilities.address
                'local_contact' => $feasibility->spoc_name ?? '',        // feasibilities.spoc_name
                'state' => $feasibility->state ?? '',                    // feasibilities.state
                'gst_number' => $client->gstin ?? '',                    // clients.gstin
                
                // ✅ Network Configuration from Feasibility table
                'link_type' => $feasibility->type_of_service ?? '',      // feasibilities.type_of_service
                'speed_in_mbps' => $feasibility->speed ?? '',            // feasibilities.speed
                'no_of_links' => $feasibility->no_of_links ?? $purchaseOrder->no_of_links ?? 1, // feasibilities.no_of_links
                
                // Vendor Information from Feasibility Status
                'vendor' => $feasibilityStatus->vendor1_name ?? '',
                
                // Pricing from Purchase Order
                'arc_cost' => $purchaseOrder->arc_per_link ?? 0,
                'otc_cost' => $purchaseOrder->otc_per_link ?? 0,
                'static_ip_cost' => $purchaseOrder->static_ip_cost_per_link ?? 0,
                
                // PO Details
                'po_number' => $purchaseOrder->po_number,
                'po_date' => $purchaseOrder->po_date,
            ]);
            
            Log::info("Deliverable created successfully with ID: {$deliverable->id} for Purchase Order: {$purchaseOrder->po_number}");
            
        } catch (\Exception $e) {
            Log::error("Failed to create deliverable from purchase order: " . $e->getMessage());
        }
    }

private function validatePricing($feas, $request, $noOfLinks)
{
    if (!$feas || !$noOfLinks) {
        return null;
    }

    // ✅ Get feasibility to check vendor_type (use relationship from FeasibilityStatus)
    $feasibility = $feas->feasibility;
    
    // 🐛 Debug logging
    Log::info('validatePricing called', [
        'feasibility_id' => $feasibility ? $feasibility->id : 'null',
        'vendor_type' => $feasibility ? $feasibility->vendor_type : 'null'
    ]);
    
    // ✅ Skip 20% validation if vendor_type is Self (UBN, UBS, UBL, INF)
    $selfVendors = ['UBN', 'UBS', 'UBL', 'INF'];
    if ($feasibility && in_array($feasibility->vendor_type, $selfVendors)) {
        Log::info('Self vendor detected - skipping validation', ['vendor_type' => $feasibility->vendor_type]);
        // Self vendor - No 20% advance needed, skip validation
        return null;
    }
    
    Log::info('Proceeding with 20% validation', ['vendor_type' => $feasibility ? $feasibility->vendor_type : 'null']);

    // Get minimum vendor values per link
    $vendorARCs = [];
    $vendorOTCs = [];
    $vendorStaticIPs = [];

    for ($v = 1; $v <= 4; $v++) {
        $arc = $feas->{"vendor{$v}_arc"};
        $otc = $feas->{"vendor{$v}_otc"};
        $sip = $feas->{"vendor{$v}_static_ip_cost"};

        if ($arc > 0) $vendorARCs[] = $arc;
        if ($otc > 0) $vendorOTCs[] = $otc;
        if ($sip > 0) $vendorStaticIPs[] = $sip;
    }

    // Prevent min() error: use 0 when empty
    $minARC = !empty($vendorARCs) ? min($vendorARCs) : 0;
    $minOTC = !empty($vendorOTCs) ? min($vendorOTCs) : 0;
    $minSIP = !empty($vendorStaticIPs) ? min($vendorStaticIPs) : 0;

    // PER-LINK VALIDATION
    for ($i = 1; $i <= $noOfLinks; $i++) {

        $arc = (float)$request->input("arc_link_{$i}");
        $otc = (float)$request->input("otc_link_{$i}");
        $sip = (float)$request->input("static_ip_link_{$i}");

        // ARC
        if ($minARC > 0) {

            if (abs($arc - $minARC) < 0.01) {
                return "MATCHED PRICE (NOT ALLOWED) - ARC for Link {$i} (₹{$arc}) matches feasibility ARC of ₹{$minARC}.";
            }

            if ($arc < ($minARC * 1.20)) {
                return "LOW PRICE (NOT ALLOWED) - ARC for Link {$i} (₹{$arc}) must be at least 20% higher than feasibility ARC ₹{$minARC}. Minimum required: ₹" . ($minARC * 1.20);
            }
        }

        // OTC
        if ($minOTC > 0) {

            if (abs($otc - $minOTC) < 0.01) {
                return "MATCHED PRICE (NOT ALLOWED) - OTC for Link {$i} (₹{$otc}) matches feasibility OTC of ₹{$minOTC}.";
            }

            if ($otc < ($minOTC * 1.20)) {
                return "LOW PRICE (NOT ALLOWED) - OTC for Link {$i} (₹{$otc}) must be at least 20% higher than feasibility OTC ₹{$minOTC}. Minimum required: ₹" . ($minOTC * 1.20);
            }
        }

        // Static IP
        if ($minSIP > 0) {

            if (abs($sip - $minSIP) < 0.01) {
                return "MATCHED PRICE (NOT ALLOWED) - Static IP for Link {$i} (₹{$sip}) matches feasibility Static IP of ₹{$minSIP}.";
            }

            if ($sip < ($minSIP * 1.20)) {
                return "LOW PRICE (NOT ALLOWED) - Static IP for Link {$i} (₹{$sip}) must be at least 20% higher than feasibility Static IP ₹{$minSIP}. Minimum required: ₹" . ($minSIP * 1.20);
            }
        }
    }

    return null;
}


}