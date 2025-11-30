<?php

namespace App\Http\Controllers;

use App\Models\NotificationSetting;
use App\Models\NotificationLog;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\TemplateHelper;

class NotificationSettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $permissions = TemplateHelper::getUserMenuPermissions('Notification Settings');

        if (!$permissions->can_view) {
            abort(403, 'Unauthorized access');
        }

        // Get or create settings for user's company
        if ($user->user_type_id == 1 || $user->user_type_id == 2) {
            // Superadmin/Admin can manage all companies
            $companies = Company::all();
            $companyId = request('company_id', $companies->first()->id ?? null);
        } else {
            // Regular users only see their companies
            $companies = $user->companies;
            $companyId = $companies->first()->id ?? null;
        }

        $settings = NotificationSetting::firstOrCreate(
            ['company_id' => $companyId],
            [
                'sla_breach_enabled' => true,
                'sla_breach_to_client' => true,
                'sla_breach_to_operations' => true,
                'link_down_enabled' => true,
                'link_down_threshold_minutes' => 5,
                'high_latency_enabled' => true,
                'high_latency_threshold_ms' => 50,
                'high_latency_duration_minutes' => 10,
                'high_packet_loss_enabled' => true,
                'high_packet_loss_threshold_percent' => 2.0,
                'high_packet_loss_duration_minutes' => 10,
                'email_enabled' => true,
                'whatsapp_enabled' => false,
                'alert_cooldown_minutes' => 30,
            ]
        );

        // Get recent notification logs
        $recentNotifications = NotificationLog::where('company_id', $companyId)
            ->with('clientLink.deliverable')
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        return view('notification_settings.index', compact('settings', 'permissions', 'companies', 'companyId', 'recentNotifications'));
    }

    public function update(Request $request, $id)
    {
        $permissions = TemplateHelper::getUserMenuPermissions('Notification Settings');

        if (!$permissions->can_edit) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'sla_breach_enabled' => 'boolean',
            'sla_breach_recipients' => 'nullable|string',
            'sla_breach_to_client' => 'boolean',
            'sla_breach_to_operations' => 'boolean',
            'link_down_enabled' => 'boolean',
            'link_down_threshold_minutes' => 'integer|min:1|max:60',
            'link_down_recipients' => 'nullable|string',
            'high_latency_enabled' => 'boolean',
            'high_latency_threshold_ms' => 'integer|min:10|max:500',
            'high_latency_duration_minutes' => 'integer|min:5|max:60',
            'high_latency_recipients' => 'nullable|string',
            'high_packet_loss_enabled' => 'boolean',
            'high_packet_loss_threshold_percent' => 'numeric|min:0.1|max:50',
            'high_packet_loss_duration_minutes' => 'integer|min:5|max:60',
            'high_packet_loss_recipients' => 'nullable|string',
            'whatsapp_enabled' => 'boolean',
            'whatsapp_numbers' => 'nullable|string',
            'email_enabled' => 'boolean',
            'email_from' => 'nullable|email',
            'alert_cooldown_minutes' => 'integer|min:5|max:180',
        ]);

        $settings = NotificationSetting::findOrFail($id);

        // Convert comma-separated strings to arrays
        $validated['sla_breach_recipients'] = $request->sla_breach_recipients 
            ? array_map('trim', explode(',', $request->sla_breach_recipients))
            : null;

        $validated['link_down_recipients'] = $request->link_down_recipients 
            ? array_map('trim', explode(',', $request->link_down_recipients))
            : null;

        $validated['high_latency_recipients'] = $request->high_latency_recipients 
            ? array_map('trim', explode(',', $request->high_latency_recipients))
            : null;

        $validated['high_packet_loss_recipients'] = $request->high_packet_loss_recipients 
            ? array_map('trim', explode(',', $request->high_packet_loss_recipients))
            : null;

        $validated['whatsapp_numbers'] = $request->whatsapp_numbers 
            ? array_map('trim', explode(',', $request->whatsapp_numbers))
            : null;

        $settings->update($validated);

        return redirect()->route('notification-settings.index')
            ->with('success', 'Notification settings updated successfully!');
    }

    public function logs()
    {
        $user = Auth::user();
        $permissions = TemplateHelper::getUserMenuPermissions('Notification Settings');

        if (!$permissions->can_view) {
            abort(403, 'Unauthorized access');
        }

        if ($user->user_type_id == 1 || $user->user_type_id == 2) {
            $companyIds = Company::pluck('id');
        } else {
            $companyIds = $user->companies()->pluck('companies.id');
        }

        $logs = NotificationLog::whereIn('company_id', $companyIds)
            ->with(['clientLink.deliverable', 'clientLink.router'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('notification_settings.logs', compact('logs', 'permissions'));
    }
}
