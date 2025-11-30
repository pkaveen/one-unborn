<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
        }
        .alert-box {
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 15px;
            margin: 20px 0;
        }
        .stats {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .stat-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .stat-label {
            font-weight: bold;
            color: #6b7280;
        }
        .stat-value {
            color: #111827;
        }
        .breach {
            color: #ef4444;
            font-weight: bold;
        }
        .breach-details {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            background: #3b82f6;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0;">⚠️ SLA Breach Alert</h1>
            <p style="margin: 10px 0 0;">{{ \Carbon\Carbon::parse($report->report_month)->format('F Y') }}</p>
        </div>

        <div class="content">
            <div class="alert-box">
                <strong>Attention Required:</strong> One or more SLA commitments were not met for your link during {{ \Carbon\Carbon::parse($report->report_month)->format('F Y') }}.
            </div>

            <h2>Link Information</h2>
            <div class="stats">
                <div class="stat-row">
                    <span class="stat-label">Link ID:</span>
                    <span class="stat-value">{{ $link->deliverable->deliverable_id ?? 'N/A' }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Circuit ID:</span>
                    <span class="stat-value">{{ $link->circuit_id ?? 'N/A' }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Router:</span>
                    <span class="stat-value">{{ $link->router->name }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Interface:</span>
                    <span class="stat-value">{{ $link->interface_name }}</span>
                </div>
            </div>

            <h2>Performance Metrics</h2>
            <div class="stats">
                <div class="stat-row">
                    <span class="stat-label">Uptime:</span>
                    <span class="stat-value {{ $report->uptime_percentage < $link->sla_uptime ? 'breach' : '' }}">
                        {{ number_format($report->uptime_percentage, 2) }}% 
                        (Required: {{ $link->sla_uptime }}%)
                    </span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Total Downtime:</span>
                    <span class="stat-value">
                        {{ gmdate('H:i:s', $report->total_downtime_seconds) }}
                    </span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Average Latency:</span>
                    <span class="stat-value {{ $report->avg_latency_ms > $link->sla_latency ? 'breach' : '' }}">
                        {{ number_format($report->avg_latency_ms, 2) }} ms 
                        (Max: {{ $link->sla_latency }} ms)
                    </span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Average Packet Loss:</span>
                    <span class="stat-value {{ $report->avg_packet_loss_percent > $link->sla_packet_loss ? 'breach' : '' }}">
                        {{ number_format($report->avg_packet_loss_percent, 2) }}% 
                        (Max: {{ $link->sla_packet_loss }}%)
                    </span>
                </div>
            </div>

            @if($report->breach_details && count($report->breach_details) > 0)
            <h2>Breach Details</h2>
            <div class="breach-details">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($report->breach_details as $detail)
                        <li style="margin-bottom: 10px;">{{ $detail }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div style="text-align: center;">
                <a href="{{ config('app.url') }}/client/sla-reports" class="button">
                    View Full Report
                </a>
            </div>

            <p style="margin-top: 30px; color: #6b7280;">
                <strong>Next Steps:</strong><br>
                Our technical team has been notified and will investigate this SLA breach. 
                If you have any concerns or questions, please contact your account manager.
            </p>
        </div>

        <div class="footer">
            <p>This is an automated notification from One Unborn Client Portal.</p>
            <p>&copy; {{ date('Y') }} One Unborn. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
