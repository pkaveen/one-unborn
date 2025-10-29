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



}
