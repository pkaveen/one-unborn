<?php

namespace App\Http\Controllers;

use App\Helpers\TemplateHelper;
use App\Models\Client;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\Feasibility;
use App\Models\FeasibilityStatus;
use Illuminate\Support\Facades\Auth;

class FeasibilityController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Check if user is super admin
        if ($user->is_superuser || $user->user_type_id == 1) {
            // Super admin can see all feasibilities
            $feasibilities = Feasibility::with(['client', 'company'])->get();
        } else {
            // Regular users see only feasibilities from their companies
            $userCompanies = $user->companies->pluck('id');
            $feasibilities = Feasibility::with(['client', 'company'])
                ->whereIn('company_id', $userCompanies)
                ->get();
        }

        $permissions = TemplateHelper::getUserMenuPermissions('User Type') ?? (object)[
    'can_add' => true,
    'can_edit' => true,
    'can_delete' => true,
    'can_view' => true,
];
        return view('feasibility.index', compact('feasibilities', 'permissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Check if user is super admin
        if ($user->is_superuser || $user->user_type_id == 1) {
            // Super admin can see all companies
            $companies = Company::all();
        } else {
            // Regular users see only their assigned companies
            $companies = $user->companies;
        }
        
        // Clients are always independent - show all clients to everyone
        $clients = Client::all();
        
        return view('feasibility.create', compact('clients', 'companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type_of_service' => 'required',
            'company_id' => 'required|exists:companies,id',
            'client_id' => 'required',
            'pincode' => 'required',
            'state' => 'required',
            'district' => 'required',
            'area' => 'required',
            'address' => 'required',
            'spoc_name' => 'required',
            'spoc_contact1' => 'required',
            'spoc_contact2' => 'nullable',
            'spoc_email' => 'nullable|email',
            'no_of_links' => 'required',
            'speed' => 'required',
            'vendor_type' => 'required',
            'static_ip' => 'required',
            'expected_delivery' => 'required|date',
            'expected_activation' => 'required|date',
            'status' => 'required|in:Active,Inactive',
        ]);

         // 🧠 Convert DD-MM-YYYY to YYYY-MM-DD before saving
    $validated['expected_delivery'] = date('Y-m-d', strtotime(str_replace('-', '/', $request->expected_delivery)));
    $validated['expected_activation'] = date('Y-m-d', strtotime(str_replace('-', '/', $request->expected_activation)));
    
    // Add created_by
    $validated['created_by'] = Auth::user()->id;

        $feasibility = Feasibility::create($validated);

        // ⚙️ Automatically create feasibility status entry for operations
        FeasibilityStatus::create([
            'feasibility_id' => $feasibility->id,
            'status' => 'Open',
        ]);

        return redirect()->route('sm.feasibility.open')->with('success', 'Feasibility added successfully!');
    }

    public function edit($id)
{
    $feasibility = Feasibility::findOrFail($id);
    $user = Auth::user();
    
    // Check if user is super admin
    if ($user->is_superuser || $user->user_type_id == 1) {
        // Super admin can see all companies
        $companies = Company::all();
    } else {
        // Regular users see only their assigned companies
        $companies = $user->companies;
    }
    
    // Clients are always independent - show all clients to everyone
    $clients = Client::all();

    return view('feasibility.edit', compact('feasibility', 'clients', 'companies'));
}


    public function update(Request $request, Feasibility $feasibility)
    {
        $validated = $request->validate([
            'type_of_service' => 'required',
            'company_id' => 'required|exists:companies,id',
            'client_id' => 'required',
            'pincode' => 'required',
            'state' => 'required',
            'district' => 'required',
            'area' => 'required',
            'address' => 'required',
            'spoc_name' => 'required',
            'spoc_contact1' => 'required',
            'spoc_contact2' => 'nullable',
            'spoc_email' => 'nullable|email',
            'no_of_links' => 'required',
            'vendor_type' => 'required',
            'speed' => 'required',
            'static_ip' => 'required',
            'expected_delivery' => 'required|date',
            'expected_activation' => 'required|date',
            'status' => 'required|in:Active,Inactive',
        ]);

        // 🧠 Convert DD-MM-YYYY to YYYY-MM-DD before saving
    $validated['expected_delivery'] = date('Y-m-d', strtotime(str_replace('-', '/', $request->expected_delivery)));
    $validated['expected_activation'] = date('Y-m-d', strtotime(str_replace('-', '/', $request->expected_activation)));

        $feasibility->update($validated);
        return redirect()->route('feasibility.index')->with('success', 'Feasibility updated successfully!');
    }

    public function destroy(Feasibility $feasibility)
    {
        $feasibility->delete();
        return redirect()->route('feasibility.index')->with('success', 'Feasibility deleted successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $feasibility = Feasibility::with('client', 'feasibilityStatus')->findOrFail($id);
        return view('feasibility.show', compact('feasibility'));
    }

    public function view($id)
    {
        $feasibility = Feasibility::with('client', 'feasibilityStatus')->findOrFail($id);
        return view('feasibility.view', compact('feasibility'));
    }

     public function toggleStatus($id)
{
    $feasibility = Feasibility::findOrFail($id);

    // Toggle Active/Inactive
    $feasibility->status = $feasibility->status === 'Active' ? 'Inactive' : 'Active';
    $feasibility->save();

    return redirect()->route('feasibility.index')
                     ->with('success', 'Feasibility status updated successfully.');
}



}
