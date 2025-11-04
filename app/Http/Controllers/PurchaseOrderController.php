<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\Feasibility;
use App\Models\FeasibilityStatus;
use App\Helpers\TemplateHelper;

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
        $validated = $request->validate([
            'feasibility_id' => 'required|exists:feasibilities,id',
            'po_number' => 'required|string|max:255|unique:purchase_orders,po_number',
            'po_date' => 'required|date',
            'arc_per_link' => 'required|numeric|min:0',
            'otc_per_link' => 'required|numeric|min:0',
            'static_ip_cost_per_link' => 'required|numeric|min:0',
            'no_of_links' => 'required|integer|min:1',
            'contract_period' => 'required|integer|min:1',
        ]);

        $validated['status'] = 'Active';

        PurchaseOrder::create($validated);

        return redirect()->route('sm.purchaseorder.index')
            ->with('success', 'Purchase Order created successfully!');
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
        
        $validated = $request->validate([
            'feasibility_id' => 'required|exists:feasibilities,id',
            'po_number' => 'required|string|max:255|unique:purchase_orders,po_number,' . $id,
            'po_date' => 'required|date',
            'arc_per_link' => 'required|numeric|min:0',
            'otc_per_link' => 'required|numeric|min:0',
            'static_ip_cost_per_link' => 'required|numeric|min:0',
            'no_of_links' => 'required|integer|min:1',
            'contract_period' => 'required|integer|min:1',
        ]);

        $purchaseOrder->update($validated);

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
        $feasibility = Feasibility::with('client')->findOrFail($id);
        
        return response()->json([
            'client' => $feasibility->client,
            'feasibility' => $feasibility,
            'no_of_links' => $feasibility->no_of_links
        ]);
    }
}
