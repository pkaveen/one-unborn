<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMenuPrivilege extends Model
{
     protected $fillable = [
        'user_id', 'menu_id', 'can_menu', 'can_add', 'can_edit', 'can_delete', 'can_view',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
