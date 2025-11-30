<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Client extends Authenticatable
{
   use HasFactory;

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($client) {
            if (empty($client->client_code)) {
                $client->client_code = \App\Services\PrefixGenerator::generateClientCode();
            }
        });
    }

    protected $fillable = [
        'user_name',
        'client_code',
        'pan_number',
        'client_name',
        'business_display_name',
        'company_id',
        'address1', 
        'address2', 
        'address3',
        'city', 
        'state', 
        'country',
         'pincode',
        'billing_spoc_name', 'billing_spoc_contact',
         'billing_spoc_email', 
         'gstin',
         'invoice_email',
        'invoice_cc',
        'support_spoc_name', 
        'support_spoc_mobile',
         'support_spoc_email',
         'status',
         // Client Portal Authentication
         'portal_username',
         'portal_password',
         'portal_active',
         'portal_last_login',
    ];

    protected $hidden = [
        'portal_password',
        'remember_token',
    ];

    protected $casts = [
        'portal_active' => 'boolean',
        'portal_last_login' => 'datetime',
    ];

        /**
         * Get the password for authentication (portal_password field)
         */
        public function getAuthPassword()
        {
            return $this->portal_password;
        }

        /**
         * Get the identifier for authentication (portal_username field)
         */
        public function getAuthIdentifierName()
        {
            return 'portal_username';
        }

        /**
         * Get the unique identifier for authentication
         */
        public function getAuthIdentifier()
        {
            return $this->{$this->getAuthIdentifierName()};
        }

    // Relationship with Company
public function company() {
    return $this->belongsTo(Company::class);
}

// Relationship with GSTINs
public function gstins()
{
    return $this->hasMany(Gstin::class, 'entity_id')->where('entity_type', 'client');
}


}
