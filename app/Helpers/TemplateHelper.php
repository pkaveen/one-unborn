<?php

namespace App\Helpers;

use App\Models\UserMenuPrivilege;
use Illuminate\Support\Facades\Auth;

class TemplateHelper
{
    /**
     * Replace placeholders like {{name}}, {{company_name}} etc.
     */
    public static function renderTemplate($content, $data = [])
    {
        foreach ($data as $key => $value) {
            $content = str_replace('{{'.$key.'}}', $value, $content);
        }
        return $content;
    }

    /**
     * ✅ Get current logged-in user's permissions for a given menu name.
     */
    public static function getUserMenuPermissions($menuName)
    {
        $user = Auth::user();

        if (!$user) {
            return (object)[
                'can_menu' => false,
                'can_add' => false,
                'can_edit' => false,
                'can_delete' => false,
                'can_view' => false,
            ];
        }

        $priv = UserMenuPrivilege::where('user_id', $user->id)
            ->whereHas('menu', function ($query) use ($menuName) {
                $query->where('name', $menuName);
            })
            ->with('menu')
            ->first();

       return (object)[
        'can_menu' => (bool)($priv->can_menu ?? false),
        'can_add' => (bool)($priv->can_add ?? false),
        'can_edit' => (bool)($priv->can_edit ?? false),
        'can_delete' => (bool)($priv->can_delete ?? false),
        'can_view' => (bool)($priv->can_view ?? false),
        ];
    }
}
