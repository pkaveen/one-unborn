<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deliverables extends Model
{
    protected $fillable = [
        'feasibility_id',
        'purchase_order_id',
        'status',
        'delivery_id',
        'site_address',
        'local_contact',
        'state',
        'gst_number',
        'link_type',
        'speed_in_mbps',
        'no_of_links',
        'vendor',
        'circuit_id',
        'plans_name',
        'speed_in_mbps_plan',
        'no_of_months_renewal',
        'date_of_activation',
        'sla',
        'mode_of_delivery',
        'pppoe_username',
        'pppoe_password',
        'static_ip_address',
        'static_vlan',
        'static_subnet_mask',
        'static_gateway',
        'static_vlan_tag',
        'status_of_link',
        'otc_extra_charges',
        'otc_bill_file',
        'delivered_at',
        'delivered_by',
        'delivery_notes',
        'arc_cost',
        'otc_cost',
        'static_ip_cost',
        'po_number',
        'po_date'
    ];

    protected $casts = [
        'date_of_activation' => 'date',
        'delivered_at' => 'datetime',
        'otc_extra_charges' => 'decimal:2'
    ];

    // Relationship with Feasibility
    public function feasibility(): BelongsTo
    {
        return $this->belongsTo(Feasibility::class);
    }

    // Relationship with Purchase Order
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    // Helper method to generate delivery ID
    public static function generateDeliveryId()
    {
        $prefix = 'DEL';
        $year = date('Y');
        $lastRecord = self::whereYear('created_at', $year)
                         ->orderBy('id', 'desc')
                         ->first();
        
        $sequence = $lastRecord ? (int)substr($lastRecord->delivery_id, -4) + 1 : 1;
        
        return $prefix . $year . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    // Boot method to auto-generate delivery ID
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($deliverable) {
            if (!$deliverable->delivery_id) {
                $deliverable->delivery_id = self::generateDeliveryId();
            }
        });
    }

    // Scope for different statuses
    public function scopeOpen($query)
    {
        return $query->where('status', 'Open');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'InProgress');
    }

    public function scopeDelivery($query)
    {
        return $query->where('status', 'Delivery');
    }
}
