<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Models\UserMenuPrivilege;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::orderBy('id')->get();
        return view('menus.index', compact('menus'));
    }

    public function create()
    {
        return view('menus.create');
    }
     public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            // 'user_type' => 'required',
        ]);

        Menu::create($request->only(['name', 'route', 'icon', 'user_type']));
        return redirect()->route('menus.index')->with('success', 'Menu created successfully!');
    }

    public function edit(Menu $menu)
    {
        return view('menus.edit', compact('menu'));
    }
    public function update(Request $request, Menu $menu)
    {
        $menu->update($request->all());
        return redirect()->route('menus.index')->with('success', 'Menu updated successfully!');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        return redirect()->route('menus.index')->with('success', 'Menu deleted successfully!');
    }

    // ✅ Show privilege edit page
    public function editPrivileges($userId)
    {
        $user = User::findOrFail($userId);

        // Show only menus relevant to user type
        $menus = Menu::whereIn('user_type', [$user->user_type, 'All'])
                    ->orderBy('id')
                    ->get();

        // 🟢 Load existing privileges for this user
        $userPrivileges = UserMenuPrivilege::where('user_id', $userId)
                            ->get()
                            ->keyBy('menu_id');

        return view('menus.editprivileges', compact('user', 'menus', 'userPrivileges'));
    }

    // ✅ Save privileges
    public function updatePrivileges(Request $request, $userId)
    {
        $privileges = $request->input('privileges', []);

        // Delete all old privileges first (to prevent duplicates)
        UserMenuPrivilege::where('user_id', $userId)->delete();

        // Insert new privileges
        foreach ($privileges as $menuId => $rights) {
            UserMenuPrivilege::create([
                'user_id' => $userId,
                'menu_id' => $menuId,
                'can_menu' => isset($rights['can_menu']),
                'can_add' => isset($rights['can_add']),
                'can_edit' => isset($rights['can_edit']),
                'can_delete' => isset($rights['can_delete']),
                'can_view' => isset($rights['can_view']),
            ]);
        }
        return redirect()->route('users.index')->with('success', 'Menu privileges updated successfully!');
    }
}
