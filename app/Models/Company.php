<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'cin_llpin',
        'contact_no',
        'phone_no',
        'email_1',
        'email_2',
        'address',
        'billing_logo',
        'billing_sign_normal',
        'billing_sign_digital',
        'gst_no',
        'pan_number',
        'tan_number',
        'color',
        'logo',
        'footer',
        'status',
    ];

    // Company.php
public function templates()
{
    return $this->hasMany(EmailTemplate::class, 'company_id');
    // return $this->belongsToMany(User::class, 'company_user', 'company_id', 'user_id');
}

public function users()
{
    return $this->hasMany(User::class, 'company_id', 'id');
}

}
