<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Helpers\TemplateHelper;
use App\Models\Deliverables;
use App\Models\Feasibility;
use App\Models\FeasibilityStatus;

class DeliverablesController extends Controller
{
    // Operations Methods for Menu
    // ====================================

    public function operationsOpen()
    {
        $records = Deliverables::with(['feasibility', 'feasibility.client', 'purchaseOrder'])
            ->where('status', 'Open')
            ->whereNotNull('purchase_order_id') // ✅ ONLY purchase order-based deliverables
            ->latest()
            ->get();

        return view('operations.deliverables.open', compact('records'));
    }

    public function operationsInProgress()
    {
        $records = Deliverables::with(['feasibility', 'feasibility.client', 'purchaseOrder'])
            ->where('status', 'InProgress')
            ->whereNotNull('purchase_order_id') // ✅ ONLY purchase order-based deliverables
            ->latest()
            ->get();

        return view('operations.deliverables.inprogress', compact('records'));
    }

    public function operationsDelivery()
    {
        $records = Deliverables::with(['feasibility', 'feasibility.client', 'purchaseOrder'])
            ->where('status', 'Delivery')
            ->whereNotNull('purchase_order_id') // ✅ ONLY purchase order-based deliverables
            ->latest()
            ->get();

        return view('operations.deliverables.delivery', compact('records'));
    }

    public function operationsView($id)
    {
        $record = Deliverables::with(['feasibility', 'feasibility.client', 'purchaseOrder'])->findOrFail($id);
        return view('operations.deliverables.view', compact('record'));
    }

    public function operationsEdit($id)
    {
        $record = Deliverables::with(['feasibility', 'feasibility.client', 'purchaseOrder'])->findOrFail($id);
        
        // Get all vendors for dropdown (if needed)
        $vendors = \App\Models\Vendor::orderBy('vendor_name')->get();
        
        return view('operations.deliverables.edit', compact('record', 'vendors'));
    }

    public function operationsSave(Request $request, $id)
    {
        $data = $request->validate([
            'site_address' => 'nullable|string',
            'local_contact' => 'nullable|string',
            'state' => 'nullable|string',
            'gst_number' => 'nullable|string',
            'status' => 'nullable|in:Open,InProgress,Delivery',
            'link_type' => 'nullable|string',
            'speed_in_mbps' => 'nullable|string',
            'no_of_links' => 'nullable|integer',
            'vendor' => 'nullable|string',
            'circuit_id' => 'nullable|string',
            'plans_name' => 'nullable|string',
            'speed_in_mbps_plan' => 'nullable|string',
            'no_of_months_renewal' => 'nullable|integer',
            'date_of_activation' => 'nullable|date',
            'sla' => 'nullable|string',
            'mode_of_delivery' => 'nullable|string',
            'pppoe_username' => 'nullable|string',
            'pppoe_password' => 'nullable|string',
            'static_ip_address' => 'nullable|string',
            'static_vlan' => 'nullable|string',
            'static_subnet_mask' => 'nullable|string',
            'static_gateway' => 'nullable|string',
            'static_vlan_tag' => 'nullable|string',
            'status_of_link' => 'nullable|string',
            'otc_extra_charges' => 'nullable|numeric',
            'delivery_notes' => 'nullable|string',
            'delivered_by' => 'nullable|string',
            'action' => 'required|string'
        ]);

        $record = Deliverables::findOrFail($id);
        $previousStatus = $record->status;
        
        // Update the record with form data
        $record->update($data);
        
        // Handle different actions
        $action = $request->input('action');
        $message = '';
        $redirectRoute = '';
        
        switch ($action) {
            case 'save':
                $message = 'Deliverable information saved successfully!';
                $redirectRoute = 'operations.deliverables.open';
                break;
                
            case 'move_to_progress':
                $record->update(['status' => 'InProgress']);
                $message = 'Deliverable moved to In Progress successfully!';
                $redirectRoute = 'operations.deliverables.inprogress';
                break;
                
            case 'complete':
                $record->update([
                    'status' => 'Delivery',
                    'delivered_at' => now(),
                    'delivered_by' => $request->input('delivered_by') ?: Auth::user()->name ?? 'System'
                ]);
                $message = 'Deliverable marked as delivered successfully!';
                $redirectRoute = 'operations.deliverables.delivery';
                break;
                
            default:
                $message = 'Deliverable updated successfully!';
                $redirectRoute = 'operations.deliverables.open';
        }
        
        return redirect()->route($redirectRoute)->with('success', $message);
    }

    public function operationsSubmit(Request $request, $id)
    {
        $record = Deliverables::findOrFail($id);
        
        // Mark as delivered
        $record->update([
            'status' => 'Delivery',
            'delivered_at' => now(),
            'delivered_by' => Auth::user()->name ?? 'System'
        ]);
        
        return redirect()->route('operations.deliverables.delivery')
                        ->with('success', 'Deliverable marked as delivered successfully!');
    }

    // Method to create deliverable from feasibility
    public function createFromFeasibility($feasibilityId)
    {
        $feasibility = Feasibility::findOrFail($feasibilityId);
        
        // Check if deliverable already exists for this feasibility
        $existingDeliverable = Deliverables::where('feasibility_id', $feasibilityId)->first();
        
        if ($existingDeliverable) {
            return redirect()->route('operations.deliverables.view', $existingDeliverable->id)
                           ->with('info', 'Deliverable already exists for this feasibility.');
        }
        
        // Create new deliverable with feasibility data
        $deliverable = Deliverables::create([
            'feasibility_id' => $feasibilityId,
            'status' => 'Open',
            'site_address' => $feasibility->site_address ?? '',
            'local_contact' => $feasibility->contact_person ?? '',
            'state' => $feasibility->state ?? '',
            'link_type' => $feasibility->connection_type ?? '',
            'speed_in_mbps' => $feasibility->bandwidth ?? '',
            'no_of_links' => 1, // Default
        ]);
        
        return redirect()->route('operations.deliverables.edit', $deliverable->id)
                        ->with('success', 'Deliverable created successfully! Please fill in the delivery details.');
    }
}
