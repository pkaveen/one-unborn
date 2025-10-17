<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'timezone', 'date_format', 'language', 'currency_symbol', 'fiscal_start_month'
    ];
}
