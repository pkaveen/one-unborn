<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkMonitoringData extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'link_id',
        'timestamp',
        'rx_bytes',
        'tx_bytes',
        'rx_packets',
        'tx_packets',
        'rx_errors',
        'tx_errors',
        'latency_ms',
        'packet_loss_percent',
        'link_status',
        'rx_rate_mbps',
        'tx_rate_mbps',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'rx_bytes' => 'integer',
        'tx_bytes' => 'integer',
        'rx_packets' => 'integer',
        'tx_packets' => 'integer',
        'rx_errors' => 'integer',
        'tx_errors' => 'integer',
        'latency_ms' => 'decimal:2',
        'packet_loss_percent' => 'decimal:2',
        'rx_rate_mbps' => 'decimal:2',
        'tx_rate_mbps' => 'decimal:2',
    ];

    public function link()
    {
        return $this->belongsTo(ClientLink::class, 'link_id');
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('timestamp', '>=', now()->subHours($hours));
    }

    public function scopeForLink($query, $linkId)
    {
        return $query->where('link_id', $linkId);
    }
}
