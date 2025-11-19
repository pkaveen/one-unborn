<?php

namespace App\Helpers;

use App\Models\WhatsAppSettings;
use Illuminate\Support\Facades\Log;

class WhatsAppHelper
{
    /**
     * Send WhatsApp Text Message
     */
    public static function sendMessage($mobile, $message)
    {
        $settings = WhatsAppSettings::where('is_default', 1)->first();

        if (!$settings) {
            return ['success' => false, 'message' => 'WhatsApp settings not configured'];
        }

        // Fetch from DB
        $instance_id  = $settings->unofficial_instance_id;
        $access_token = $settings->unofficial_access_token;

        $tmsg = urlencode($message);

        $url = "https://wa.unbornsms.in/api/send.php?number={$mobile}&type=text&message={$tmsg}&instance_id={$instance_id}&access_token={$access_token}";

        return self::sendCurlRequest($url, $mobile, 'text');
    }

    /**
     * Send WhatsApp Media Message (Document/Image)
     */
    public static function sendMediaMessage($mobile, $message, $mediaUrl, $filename = null)
    {
        $settings = WhatsAppSettings::where('is_default', 1)->first();

        if (!$settings) {
            return ['success' => false, 'message' => 'WhatsApp settings not configured'];
        }

        // Fetch from DB
        $instance_id  = $settings->unofficial_instance_id;
        $access_token = $settings->unofficial_access_token;

        $tmsg = urlencode($message);

        $url = "https://wa.unbornsms.in/api/send.php?number={$mobile}&type=media&message={$tmsg}&media_url={$mediaUrl}";

        if ($filename) {
            $url .= "&filename={$filename}";
        }

        $url .= "&instance_id={$instance_id}&access_token={$access_token}";

        return self::sendCurlRequest($url, $mobile, 'media');
    }

    /**
     * Central cURL Handler (Based on senior's method)
     */
    private static function sendCurlRequest($url, $toNumber, $typeOfMsg)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Accept: application/json'
            ],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $response = curl_exec($curl);
        $curlError = curl_error($curl);
        $curlErrno = curl_errno($curl);
        $httpCode  = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        // Log request
        Log::info('WhatsApp Request', [
            'url'       => $url,
            'http_code' => $httpCode,
            'curl_error'=> $curlError,
            'curl_errno'=> $curlErrno,
            'response'  => $response
        ]);

        if ($curlErrno) {
            return ['success' => false, 'message' => $curlError];
        }

        if (!$response) {
            return ['success' => false, 'message' => 'No response from API'];
        }

        $whatsappResponse = json_decode($response, true);

        // Write log file
        $logContent = date("Y-m-j g:ia") . ":to:{$toNumber};type:{$typeOfMsg};Status:" . 
                      ($whatsappResponse['status'] ?? 'unknown') . 
                      ";Message:" . ($whatsappResponse['message'] ?? 'no message') . ";";

        if (!is_dir('./mylogs')) {
            mkdir('./mylogs', 0755, true);
        }

        file_put_contents('./mylogs/whatsapp_log.txt', $logContent . PHP_EOL, FILE_APPEND);

        if (($whatsappResponse['status'] ?? '') === 'success') {
            return ['success' => true, 'message' => 'WhatsApp message sent', 'response' => $whatsappResponse];
        }

        return [
            'success' => false,
            'message' => $whatsappResponse['message'] ?? 'Unknown error'
        ];
    }

    /** Wrapper for Documents */
    public static function sendDocument($mobile, $message, $documentUrl, $filename)
    {
        return self::sendMediaMessage($mobile, $message, $documentUrl, $filename);
    }

    /** Wrapper for Images */
    public static function sendImage($mobile, $message, $imageUrl)
    {
        return self::sendMediaMessage($mobile, $message, $imageUrl);
    }
}
