<?php

namespace App\Helpers;

use App\Models\CompanySetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;


class MailHelper
{
    /**
     * Dynamically set mail configuration
     * 
     * Logic:
     * 1️⃣ If company-specific config exists, use it
     * 2️⃣ Else, use default/common email config
     */
    // public static function setMailConfig($companyId = null)
    // {
    //     $companySetting = null;

    //     // 🔹 Step 1: Try to find mail config for given company
    //     if ($companyId) {
    //         $companySetting = CompanySetting::where('company_id', $companyId)->first();
    //     }

    //     // 🔹 Step 2: If not found or incomplete, use default config (is_default = 1 or id = 1)
    //     if (!$companySetting || empty($companySetting->mail_host)) {
    //         $companySetting = CompanySetting::where('is_default', 1)->first() ?? CompanySetting::find(1);
    //     }

    //     if (!$companySetting) {
    //         Log::warning('MailHelper: No company_settings record found (companyId=' . ($companyId ?? 'null') . '). Mail not configured.');
    //         return;
    //     }
    //  // Map DB column names -> config keys
    //     $fromAddress = $companySetting->mail_from_address ?? $companySetting->company_email ?? config('mail.from.address');
    //     $fromName = $companySetting->mail_from_name ?? $companySetting->company_name ?? config('mail.from.name');

    //     Config::set('mail.default', $companySetting->mail_mailer ?? 'smtp');

    //     Config::set('mail.mailers.smtp.transport', $companySetting->mail_mailer ?? 'smtp');
    //     Config::set('mail.mailers.smtp.host', $companySetting->mail_host ?? 'smtp.gmail.com');
    //     Config::set('mail.mailers.smtp.port', $companySetting->mail_port ?? 587);
    //     Config::set('mail.mailers.smtp.encryption', $companySetting->mail_encryption ?? 'tls');
    //     Config::set('mail.mailers.smtp.username', $companySetting->mail_username ?? null);
    //     Config::set('mail.mailers.smtp.password', $companySetting->mail_password ?? null);

    //     // From header
    //     Config::set('mail.from.address', $fromAddress);
    //     Config::set('mail.from.name', $fromName);

    //     Log::info('MailHelper: Mail config applied', [
    //         'company_id' => $companyId,
    //         'host' => $companySetting->mail_host,
    //         'username' => $companySetting->mail_username,
    //         'from' => $fromAddress,
    //     ]);
    
        
    // }
    public static function setMailConfig($companyId = null)
    {
        // fallback to company 1 if missing
        $companyId = $companyId ?: 1;

        $setting = CompanySetting::where('company_id', $companyId)->first();

        if (! $setting) {
            Log::info('MailHelper: No company setting for ' . $companyId . ' - using default env');
            return;
        }

        // set config keys at runtime
        Config::set('mail.mailers.smtp.transport', $setting->mail_mailer ?? 'smtp'); // adapt to version
        Config::set('mail.mailers.smtp.host', $setting->mail_host ?? env('MAIL_HOST'));
        Config::set('mail.mailers.smtp.port', $setting->mail_port ?? env('MAIL_PORT'));
        Config::set('mail.mailers.smtp.encryption', $setting->mail_encryption ?? env('MAIL_ENCRYPTION'));
        Config::set('mail.mailers.smtp.username', $setting->mail_username ?? env('MAIL_USERNAME'));
        Config::set('mail.mailers.smtp.password', $setting->mail_password ?? env('MAIL_PASSWORD'));

        // also set mail.from so Mailable->from(...) picks it up
        if (!empty($setting->mail_from_address)) {
            Config::set('mail.from.address', $setting->mail_from_address);
            Config::set('mail.from.name', $setting->mail_from_name ?? env('MAIL_FROM_NAME'));
        }

        Log::info('📧 Mail config applied', [
            'company_id' => $companyId,
            'mailer' => $setting->mail_mailer,
            'host' => $setting->mail_host,
        ]);
    }
}
  