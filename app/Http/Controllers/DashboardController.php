<?php

namespace App\Http\Controllers;
use App\Models\Menu;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
     public function index()
    {
         $userType = Auth::user()->user_type ?? 'Employee';

    // Only menus that the logged-in user's type can view
    $menus = Menu::where('user_type', $userType)
                 ->where('can_view', 1)
                 ->get();

    return view('welcome', compact('menus'));
    }
}
