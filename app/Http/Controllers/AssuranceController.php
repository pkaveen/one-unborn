<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\TemplateHelper;

class AssuranceController extends Controller
{
    public function index()
    {
        $permissions = TemplateHelper::getUserMenuPermissions('Assurance');
        
        return view('assurance.index', compact('permissions'));
    }
}
