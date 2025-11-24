<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vendor extends Model
{
     use HasFactory;

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($vendor) {
            if (empty($vendor->vendor_code)) {
                $vendor->vendor_code = \App\Services\PrefixGenerator::generateVendorCode();
            }
        });
    }

    protected $fillable = [
        'user_name',
        'vendor_code',
        'vendor_name',
        'business_display_name',
        'address1',
        'address2',
        'address3',
        'city',
        'state',
        'country',
        'pincode',
        'contact_person_name',
        'contact_person_mobile',
        'contact_person_email',
        'gstin',
        'invoice_email',
        'invoice_cc',
        'pan_no',
        'bank_account_no',
        'ifsc_code',
        'status',
    ];
}
