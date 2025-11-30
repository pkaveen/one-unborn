<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ClientPortalUser extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'client_id',
        'username',
        'email',
        'password',
        'is_active',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_login' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function links()
    {
        return $this->hasMany(ClientLink::class, 'client_id', 'client_id');
    }
}
