<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanySetting;
use App\Models\WhatsAppSettings;
use App\Helpers\WhatsAppHelper;
use Illuminate\Support\Facades\Log;

class WhatsAppSettingsController extends Controller
{
    /**
     * Display WhatsApp settings page
     */
    public function index()
    {
        $settings = WhatsAppSettings::where('is_default', 1)->first();
        
        // Create default settings if not exists
        if (!$settings) {
            $settings = WhatsAppSettings::create(['is_default' => true]);
        }
        
        return view('settings.whatsapp', compact('settings'));
    }

    /**
     * Update WhatsApp API settings
     */
    public function update(Request $request)
    {
        $apiType = $request->input('api_type');

        if ($apiType === 'official') {
            $validated = $request->validate([
                'official_phone' => 'required|string',
                'official_account_id' => 'required|string',
                'official_enabled' => 'nullable|boolean',
            ]);
            
            // Set hardcoded values for official API
            $validated['official_access_token'] = 'YOUR_OFFICIAL_ACCESS_TOKEN';
            $validated['official_phone_id'] = 'YOUR_OFFICIAL_PHONE_ID';
        } elseif ($apiType === 'unofficial') {
            $validated = $request->validate([
                'unofficial_api_url' => 'required|url',
                'unofficial_mobile' => 'required|string',
                'unofficial_enabled' => 'nullable|boolean',
            ]);
            
            // Set hardcoded values
            $validated['unofficial_access_token'] = '68f9df1ac354c';
            $validated['unofficial_instance_id'] = '691AEEF33256E';
        } else {
            return redirect()->back()->with('error', 'Invalid API type');
        }

        // Get default WhatsApp settings
        $settings = WhatsAppSettings::where('is_default', 1)->first();

        if (!$settings) {
            $settings = WhatsAppSettings::create(['is_default' => true]);
        }

        // Update settings
        $settings->update($validated);

        return redirect()->back()->with('success', 'WhatsApp API settings updated successfully!');
    }

    /**
     * Show test message form
     */
    public function showTestForm()
    {
        return view('settings.whatsapp-test');
    }

    /**
     * Send test WhatsApp message
     */
    public function sendTestMessage(Request $request)
    {
        $validated = $request->validate([
            'mobile' => 'required|string',
            'message' => 'required|string',
        ]);

        try {
            // Send message using WhatsAppHelper (helper will handle URL encoding)
            $response = WhatsAppHelper::sendMessage($validated['mobile'], $validated['message']);
            
            // Log the response
            Log::info('WhatsApp Test Message Response', [
                'mobile' => $validated['mobile'],
                'message' => $validated['message'],
                'response' => $response
            ]);

            return redirect()->back()
                ->with('success', 'Test message sent successfully!')
                ->with('api_response', json_encode($response, JSON_PRETTY_PRINT));

        } catch (\Exception $e) {
            Log::error('WhatsApp Test Message Error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to send message: ' . $e->getMessage());
        }
    }
}


