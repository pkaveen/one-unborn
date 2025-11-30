<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientLink extends Model
{
    protected $fillable = [
        'deliverable_id',
        'client_id',
        'router_id',
        'interface_name',
        'circuit_id',
        'link_name',
        'committed_speed_mbps',
        'committed_sla_uptime',
        'committed_sla_latency_ms',
        'committed_sla_packet_loss',
        'activation_date',
        'is_active',
        'grafana_dashboard_uid',
        'monitoring_config',
    ];

    protected $casts = [
        'activation_date' => 'date',
        'is_active' => 'boolean',
        'committed_speed_mbps' => 'integer',
        'committed_sla_uptime' => 'decimal:2',
        'committed_sla_latency_ms' => 'integer',
        'committed_sla_packet_loss' => 'decimal:2',
        'monitoring_config' => 'array',
    ];

    public function deliverable()
    {
        return $this->belongsTo(Deliverables::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function router()
    {
        return $this->belongsTo(MikrotikRouter::class, 'router_id');
    }

    public function monitoringData()
    {
        return $this->hasMany(LinkMonitoringData::class, 'link_id');
    }

    public function slaReports()
    {
        return $this->hasMany(SlaReport::class, 'link_id');
    }

    public function latestMonitoringData()
    {
        return $this->hasOne(LinkMonitoringData::class, 'link_id')->latestOfMany('timestamp');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getUptimePercentageAttribute()
    {
        $latestReport = $this->slaReports()->latest('report_month')->first();
        return $latestReport ? $latestReport->uptime_percentage : null;
    }
}
