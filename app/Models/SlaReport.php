<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlaReport extends Model
{
    protected $fillable = [
        'link_id',
        'report_month',
        'total_minutes',
        'uptime_minutes',
        'downtime_minutes',
        'uptime_percentage',
        'avg_latency_ms',
        'avg_packet_loss',
        'max_latency_ms',
        'max_packet_loss',
        'sla_met',
        'sla_breach_details',
        'generated_at',
    ];

    protected $casts = [
        'report_month' => 'date',
        'total_minutes' => 'integer',
        'uptime_minutes' => 'integer',
        'downtime_minutes' => 'integer',
        'uptime_percentage' => 'decimal:2',
        'avg_latency_ms' => 'decimal:2',
        'avg_packet_loss' => 'decimal:2',
        'max_latency_ms' => 'decimal:2',
        'max_packet_loss' => 'decimal:2',
        'sla_met' => 'boolean',
        'sla_breach_details' => 'array',
        'generated_at' => 'datetime',
    ];

    public function link()
    {
        return $this->belongsTo(ClientLink::class, 'link_id');
    }

    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('report_month', $year)
                     ->whereMonth('report_month', $month);
    }

    public function scopeBreached($query)
    {
        return $query->where('sla_met', false);
    }

    public function scopeCompliant($query)
    {
        return $query->where('sla_met', true);
    }
}
