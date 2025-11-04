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
            'hardware_required' => 'required|in:0,1',
            'hardware_model_name' => 'nullable',
            'status' => 'required|in:Active,Inactive',
        ]);

         // 🧠 Convert DD-MM-YYYY to YYYY-MM-DD before saving
    $validated['expected_delivery'] = date('Y-m-d', strtotime(str_replace('-', '/', $request->expected_delivery)));
    $validated['expected_activation'] = date('Y-m-d', strtotime(str_replace('-', '/', $request->expected_activation)));
    
    // 🧠 Convert hardware_required to proper boolean
    $validated['hardware_required'] = (bool) $validated['hardware_required'];
    
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

   


   

    

   



}
