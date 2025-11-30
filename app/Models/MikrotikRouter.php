<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class MikrotikRouter extends Model
{
    protected $fillable = [
        'name',
        'management_ip',
        'api_port',
        'api_username',
        'api_password',
        'use_ssl',
        'location',
        'is_active',
        'last_poll',
        'status',
        'notes',
    ];

    protected $casts = [
        'use_ssl' => 'boolean',
        'is_active' => 'boolean',
        'last_poll' => 'datetime',
        'api_port' => 'integer',
    ];

    // Automatically encrypt password when setting
    public function setApiPasswordAttribute($value)
    {
        $this->attributes['api_password'] = Crypt::encryptString($value);
    }

    // Automatically decrypt password when getting
    public function getApiPasswordAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value; // Return as-is if decryption fails
        }
    }

    public function links()
    {
        return $this->hasMany(ClientLink::class, 'router_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOnline($query)
    {
        return $query->where('status', 'online');
    }
}
