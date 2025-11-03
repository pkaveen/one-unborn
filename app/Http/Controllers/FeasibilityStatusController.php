<?php

namespace App\Http\Controllers;

use App\Models\Feasibility;
use App\Models\FeasibilityStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\FeasibilityStatusMail;

class FeasibilityStatusController extends Controller
{
    public function index($status = 'Open')
    {
        $statuses = ['Open', 'InProgress', 'Closed'];
        $records = FeasibilityStatus::with('feasibility')
            ->where('status', $status)
            ->get();

        return view('feasibility.feasibility_status.index', compact('records', 'status', 'statuses'));
    }

    public function show($id)
    {
        $record = FeasibilityStatus::with('feasibility')->findOrFail($id);
        return view('feasibility.feasibility_status.show', compact('record'));
    }

//     public function update(Request $request, $id)
// {
//     $data = $request->validate([
//         'vendor1_name' => 'nullable|string',
//         'vendor1_arc' => 'nullable|string',
//         'vendor1_otc' => 'nullable|string',
//         'vendor1_static_ip_cost' => 'nullable|string',
//         'vendor1_delivery_timeline' => 'nullable|string',

//         'vendor2_name' => 'nullable|string',
//         'vendor2_arc' => 'nullable|string',
//         'vendor2_otc' => 'nullable|string',
//         'vendor2_static_ip_cost' => 'nullable|string',
//         'vendor2_delivery_timeline' => 'nullable|string',

//         'vendor3_name' => 'nullable|string',
//         'vendor3_arc' => 'nullable|string',
//         'vendor3_otc' => 'nullable|string',
//         'vendor3_static_ip_cost' => 'nullable|string',
//         'vendor3_delivery_timeline' => 'nullable|string',

//         'vendor4_name' => 'nullable|string',
//         'vendor4_arc' => 'nullable|string',
//         'vendor4_otc' => 'nullable|string',
//         'vendor4_static_ip_cost' => 'nullable|string',
//         'vendor4_delivery_timeline' => 'nullable|string',

//         'status' => 'required|in:Open,InProgress,Closed'
//     ]);

//     $record = FeasibilityStatus::with('feasibility')->findOrFail($id);
//     $oldStatus = $record->status;

//     $record->update($data);

//     if ($oldStatus !== $data['status']) {
//         $feasibility = $record->feasibility;
//         $recipient = $feasibility->spoc_email ?? 'admin@example.com';
//         Mail::to($recipient)->send(new FeasibilityStatusMail($feasibility, $record));
//     }

//     return redirect()->route('feasibility.status.index', $data['status'])
//         ->with('success', 'Feasibility status updated successfully.');
// }

public function edit($id)
{
    $record = FeasibilityStatus::with('feasibility')->findOrFail($id);
    return view('feasibility.feasibility_status.edit', compact('record'));
}

public function editSave(Request $request, $id)
{
    $data = $request->validate([
        'vendor1_name' => 'nullable|string',
        'vendor1_arc' => 'nullable|string',
        'vendor1_otc' => 'nullable|string',
        'vendor1_static_ip_cost' => 'nullable|string',
        'vendor1_delivery_timeline' => 'nullable|string',

        'vendor2_name' => 'nullable|string',
        'vendor2_arc' => 'nullable|string',
        'vendor2_otc' => 'nullable|string',
        'vendor2_static_ip_cost' => 'nullable|string',
        'vendor2_delivery_timeline' => 'nullable|string',

        'vendor3_name' => 'nullable|string',
        'vendor3_arc' => 'nullable|string',
        'vendor3_otc' => 'nullable|string',
        'vendor3_static_ip_cost' => 'nullable|string',
        'vendor3_delivery_timeline' => 'nullable|string',

        'vendor4_name' => 'nullable|string',
        'vendor4_arc' => 'nullable|string',
        'vendor4_otc' => 'nullable|string',
        'vendor4_static_ip_cost' => 'nullable|string',
        'vendor4_delivery_timeline' => 'nullable|string',

        'status' => 'required|in:Open,InProgress,Closed'
    ]);

    $record = FeasibilityStatus::findOrFail($id);
    $record->update($data);

    return redirect()->route('feasibility.status.index', $data['status'])
        ->with('success', 'Feasibility status updated successfully.');
}

    // ====================================
    // Sales & Marketing Methods
    // ====================================

    public function smOpen()
    {
        $records = FeasibilityStatus::with(['feasibility', 'feasibility.client'])
            ->where('status', 'Open')
            ->get();

        return view('sm.feasibility.open', compact('records'));
    }

    public function smInProgress()
    {
        $records = FeasibilityStatus::with(['feasibility', 'feasibility.client'])
            ->where('status', 'InProgress')
            ->get();

        return view('sm.feasibility.inprogress', compact('records'));
    }

    public function smClosed()
    {
        $records = FeasibilityStatus::with(['feasibility', 'feasibility.client'])
            ->where('status', 'Closed')
            ->get();

        return view('sm.feasibility.closed', compact('records'));
    }

    public function smView($id)
    {
        $record = FeasibilityStatus::with(['feasibility', 'feasibility.client'])->findOrFail($id);
        return view('sm.feasibility.view', compact('record'));
    }

    public function smEdit($id)
    {
        $record = FeasibilityStatus::with(['feasibility', 'feasibility.client'])->findOrFail($id);
        
        // ✅ Get all vendors for dropdown
        $vendors = \App\Models\Vendor::orderBy('vendor_name')->get();
        
        return view('sm.feasibility.edit', compact('record', 'vendors'));
    }

    public function smSave(Request $request, $id)
    {
        $data = $request->validate([
            'vendor1_name' => 'nullable|string',
            'vendor1_arc' => 'nullable|string',
            'vendor1_otc' => 'nullable|string',
            'vendor1_static_ip_cost' => 'nullable|string',
            'vendor1_delivery_timeline' => 'nullable|string',

            'vendor2_name' => 'nullable|string',
            'vendor2_arc' => 'nullable|string',
            'vendor2_otc' => 'nullable|string',
            'vendor2_static_ip_cost' => 'nullable|string',
            'vendor2_delivery_timeline' => 'nullable|string',

            'vendor3_name' => 'nullable|string',
            'vendor3_arc' => 'nullable|string',
            'vendor3_otc' => 'nullable|string',
            'vendor3_static_ip_cost' => 'nullable|string',
            'vendor3_delivery_timeline' => 'nullable|string',

            'vendor4_name' => 'nullable|string',
            'vendor4_arc' => 'nullable|string',
            'vendor4_otc' => 'nullable|string',
            'vendor4_static_ip_cost' => 'nullable|string',
            'vendor4_delivery_timeline' => 'nullable|string',
        ]);

        $record = FeasibilityStatus::findOrFail($id);
        $record->update($data);
        $record->update(['status' => 'InProgress']);

        return redirect()->route('sm.feasibility.inprogress')
            ->with('success', 'Feasibility moved to In Progress successfully.');
    }

    public function smSubmit(Request $request, $id)
    {
        $data = $request->validate([
            'vendor1_name' => 'nullable|string',
            'vendor1_arc' => 'nullable|string',
            'vendor1_otc' => 'nullable|string',
            'vendor1_static_ip_cost' => 'nullable|string',
            'vendor1_delivery_timeline' => 'nullable|string',

            'vendor2_name' => 'nullable|string',
            'vendor2_arc' => 'nullable|string',
            'vendor2_otc' => 'nullable|string',
            'vendor2_static_ip_cost' => 'nullable|string',
            'vendor2_delivery_timeline' => 'nullable|string',

            'vendor3_name' => 'nullable|string',
            'vendor3_arc' => 'nullable|string',
            'vendor3_otc' => 'nullable|string',
            'vendor3_static_ip_cost' => 'nullable|string',
            'vendor3_delivery_timeline' => 'nullable|string',

            'vendor4_name' => 'nullable|string',
            'vendor4_arc' => 'nullable|string',
            'vendor4_otc' => 'nullable|string',
            'vendor4_static_ip_cost' => 'nullable|string',
            'vendor4_delivery_timeline' => 'nullable|string',
        ]);

        $record = FeasibilityStatus::findOrFail($id);
        $record->update($data);
        $record->update(['status' => 'Closed']);

        return redirect()->route('sm.feasibility.closed')
            ->with('success', 'Feasibility submitted and moved to Closed successfully.');
    }

    // ====================================
    // operations Methods (Read-only)
    // ====================================

    public function operationsOpen()
    {
        $records = FeasibilityStatus::with(['feasibility', 'feasibility.client'])
            ->where('status', 'Open')
            ->get();

        return view('operations.feasibility.open', compact('records'));
    }

    public function operationsInProgress()
    {
        $records = FeasibilityStatus::with(['feasibility', 'feasibility.client'])
            ->where('status', 'InProgress')
            ->get();

        return view('operations.feasibility.inprogress', compact('records'));
    }

    public function operationsClosed()
    {
        $records = FeasibilityStatus::with(['feasibility', 'feasibility.client'])
            ->where('status', 'Closed')
            ->get();

        return view('operations.feasibility.closed', compact('records'));
    }

    public function operationsView($id)
    {
        $record = FeasibilityStatus::with(['feasibility', 'feasibility.client'])->findOrFail($id);
        return view('operations.feasibility.view', compact('record'));
    }

    public function operationsEdit($id)
    {
        $record = FeasibilityStatus::with(['feasibility', 'feasibility.client'])->findOrFail($id);
        
        // ✅ Get all vendors for dropdown
        $vendors = \App\Models\Vendor::orderBy('vendor_name')->get();
        
        return view('operations.feasibility.edit', compact('record', 'vendors'));
    }

    public function operationsSave(Request $request, $id)
    {
        $data = $request->validate([
            'vendor1_name' => 'nullable|string',
            'vendor1_arc' => 'nullable|string',
            'vendor1_otc' => 'nullable|string',
            'vendor1_static_ip_cost' => 'nullable|string',
            'vendor1_delivery_timeline' => 'nullable|string',

            'vendor2_name' => 'nullable|string',
            'vendor2_arc' => 'nullable|string',
            'vendor2_otc' => 'nullable|string',
            'vendor2_static_ip_cost' => 'nullable|string',
            'vendor2_delivery_timeline' => 'nullable|string',

            'vendor3_name' => 'nullable|string',
            'vendor3_arc' => 'nullable|string',
            'vendor3_otc' => 'nullable|string',
            'vendor3_static_ip_cost' => 'nullable|string',
            'vendor3_delivery_timeline' => 'nullable|string',

            'vendor4_name' => 'nullable|string',
            'vendor4_arc' => 'nullable|string',
            'vendor4_otc' => 'nullable|string',
            'vendor4_static_ip_cost' => 'nullable|string',
            'vendor4_delivery_timeline' => 'nullable|string',
        ]);

        $record = FeasibilityStatus::findOrFail($id);
        $record->update($data);
        $record->update(['status' => 'InProgress']);

        return redirect()->route('operations.feasibility.inprogress')
            ->with('success', 'Feasibility moved to In Progress successfully.');
    }

    public function operationsSubmit(Request $request, $id)
    {
        $data = $request->validate([
            'vendor1_name' => 'nullable|string',
            'vendor1_arc' => 'nullable|string',
            'vendor1_otc' => 'nullable|string',
            'vendor1_static_ip_cost' => 'nullable|string',
            'vendor1_delivery_timeline' => 'nullable|string',

            'vendor2_name' => 'nullable|string',
            'vendor2_arc' => 'nullable|string',
            'vendor2_otc' => 'nullable|string',
            'vendor2_static_ip_cost' => 'nullable|string',
            'vendor2_delivery_timeline' => 'nullable|string',

            'vendor3_name' => 'nullable|string',
            'vendor3_arc' => 'nullable|string',
            'vendor3_otc' => 'nullable|string',
            'vendor3_static_ip_cost' => 'nullable|string',
            'vendor3_delivery_timeline' => 'nullable|string',

            'vendor4_name' => 'nullable|string',
            'vendor4_arc' => 'nullable|string',
            'vendor4_otc' => 'nullable|string',
            'vendor4_static_ip_cost' => 'nullable|string',
            'vendor4_delivery_timeline' => 'nullable|string',
        ]);

        $record = FeasibilityStatus::findOrFail($id);
        $record->update($data);
        $record->update(['status' => 'Closed']);

        return redirect()->route('operations.feasibility.closed')
            ->with('success', 'Feasibility submitted and moved to Closed successfully.');
    }

    /**
     * Move feasibility record from S&M to operations
     */
    public function moveTooperations($id)
    {
        $record = FeasibilityStatus::findOrFail($id);
        
        // You can add additional logic here if needed
        // For now, the record stays the same but will be viewed from operations perspective
        
        return redirect()->route('operations.feasibility.open')
            ->with('success', 'Feasibility record moved to operations successfully.');
    }

    /**
     * Move feasibility record from operations to S&M
     */
    public function moveToSM($id)
    {
        $record = FeasibilityStatus::findOrFail($id);
        
        // You can add additional logic here if needed
        // For now, the record stays the same but will be viewed from S&M perspective
        
        return redirect()->route('sm.feasibility.open')
            ->with('success', 'Feasibility record moved to S&M successfully.');
    }

}
