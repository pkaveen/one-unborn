<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GSTController extends Controller
{
    public function fetchGSTDetails($gstin)
    {
        try {
            // Replace this with your real GST API URL
            $apiKey = config('services.appyflow.key'); // Store in config/services.php
            $apiUrl = "https://appyflow.in/api/verifyGST?gstNo={$gstin}&key_secret={$apiKey}";

            $response = Http::get($apiUrl);

            if ($response->successful()) {
                $data = $response->json();

                // Extract PAN from GST (middle 10 characters)
                $pan = substr($gstin, 2, 10);

                return response()->json([
                    'success' => true,
                    'data' => $data,
                    'pan' => $pan,
                ]);
            } else {
                return response()->json(['success' => false, 'message' => 'Invalid GST number']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
