<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\SystemSetting;

class SurepassService
{
    protected $apiToken;
    protected $baseUrl;
    protected $environment;

    public function __construct()
    {
        $settings = SystemSetting::first();
        $this->apiToken     = $settings->surepass_api_token ?? null;
        $this->environment  = $settings->surepass_api_environment ?? 'production';
        // Determine base URL based on environment (production vs sandbox)
        $this->baseUrl = $this->environment === 'sandbox'
            ? 'https://sandbox.surepass.app/api/v1/corporate/'
            : 'https://kyc-api.surepass.io/api/v1/corporate/';
    }

    /**
     * Fetch GSTIN details by PAN (basic endpoint)
     * 
     * @param string $pan PAN number
     * @return array
     */
    public function getGstinByPan($pan)
    {
        if (!$this->apiToken) {
            return [
                'success' => false,
                'message' => 'GSTIN API token not configured in system settings'
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . 'gstin-by-pan', [
                'id_number' => strtoupper($pan)
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['success']) && $data['success'] === true) {
                    // Fetch detailed info for each GSTIN
                    return $this->enrichGstinData($data['data']);
                }

                return [
                    'success' => false,
                    'message' => $data['message'] ?? 'Failed to fetch GSTIN details'
                ];
            }

            return [
                'success' => false,
                'message' => 'API request failed: ' . $response->status()
            ];

        } catch (\Exception $e) {
            Log::error('GSTIN API Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Error connecting to GSTIN API service: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Enrich GSTIN data with detailed information from Advanced API
     * 
     * @param array $basicData Basic GSTIN data from by-pan endpoint
     * @return array
     */
    private function enrichGstinData($basicData)
    {
        if (!isset($basicData['gstin_list']) || !is_array($basicData['gstin_list'])) {
            return [
                'success' => true,
                'data' => $basicData
            ];
        }

        $enrichedList = [];
        
        foreach ($basicData['gstin_list'] as $gstinInfo) {
            $gstin = $gstinInfo['gstin'] ?? null;
            
            if ($gstin) {
                // Fetch detailed info using GSTIN Advanced API
                $detailedInfo = $this->getGstinAdvanced($gstin);
                
                if ($detailedInfo['success']) {
                    // Merge basic and detailed data
                    $enrichedList[] = array_merge($gstinInfo, $detailedInfo['data'] ?? []);
                } else {
                    // Keep basic data if advanced fetch fails
                    $enrichedList[] = $gstinInfo;
                }
            }
        }

        $basicData['gstin_list'] = $enrichedList;
        
        return [
            'success' => true,
            'data' => $basicData
        ];
    }

    /**
     * Fetch detailed GSTIN information using Advanced API
     * 
     * @param string $gstin GSTIN number
     * @return array
     */
    public function getGstinAdvanced($gstin)
    {
        if (!$this->apiToken) {
            return [
                'success' => false,
                'message' => 'GSTIN API token not configured'
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . 'gstin-advanced', [
                'id_number' => strtoupper($gstin)
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['success']) && $data['success'] === true && isset($data['data'])) {
                    return [
                        'success' => true,
                        'data' => $data['data']
                    ];
                }

                return [
                    'success' => false,
                    'message' => $data['message'] ?? 'Failed to fetch advanced GSTIN details'
                ];
            }

            return [
                'success' => false,
                'message' => 'API request failed: ' . $response->status()
            ];

        } catch (\Exception $e) {
            Log::error('GSTIN Advanced API Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Error fetching advanced GSTIN details'
            ];
        }
    }

    /**
     * Parse and format GSTIN data from Surepass response
     * 
     * @param array $gstinData
     * @return array
     */
    public function parseGstinData($gstinData)
    {
        $formatted = [];

        if (!isset($gstinData['gstin_list']) || !is_array($gstinData['gstin_list'])) {
            return $formatted;
        }

        foreach ($gstinData['gstin_list'] as $gstin) {
            // Use advanced API data if available, fallback to basic data
            $tradeName = $gstin['legal_name'] ?? $gstin['trade_name'] ?? '';
            $businessName = $gstin['trade_name'] ?? $gstin['legal_name'] ?? '';
            
            // Get address - try principal place first, then registered office
            $address = $gstin['principal_place_address'] ?? $gstin['pradr'] ?? [];
            $addr = $this->normalizeAddressComponents($address);
            
            $formatted[] = [
                'gstin' => $gstin['gstin'] ?? '',
                'trade_name' => $businessName,
                'legal_name' => $tradeName,
                'principal_business_address' => $this->formatAddress($addr),
                'building_name' => $addr['building_name'] ?? null,
                'building_number' => $addr['building_number'] ?? null,
                'floor_number' => $addr['floor_number'] ?? null,
                'street' => $addr['street'] ?? null,
                'location' => $addr['location'] ?? null,
                'district' => $addr['district'] ?? null,
                'city' => $addr['city'] ?? null,
                'state' => $addr['state'] ?? '',
                'state_code' => substr($gstin['gstin'] ?? '', 0, 2),
                'pincode' => $addr['pincode'] ?? '',
                'status' => $gstin['status'] ?? 'Active',
                'is_primary' => false,
            ];
        }

        return $formatted;
    }

    /**
     * Format address from API response
     * 
     * @param array $address
     * @return string
     */
    private function formatAddress($address)
    {
        if (empty($address)) {
            return '';
        }

        $parts = array_filter([
            $address['building_name'] ?? '',
            $address['building_number'] ?? '',
            $address['floor_number'] ?? '',
            $address['street'] ?? '',
            $address['location'] ?? '',
            $address['district'] ?? '',
            $address['city'] ?? '',
            $address['state'] ?? '',
            $address['pincode'] ?? '',
        ]);

        return implode(', ', $parts);
    }

    /**
     * Normalize address components from different API response shapes
     * Supports both advanced 'pradr' style keys and already-normalized keys
     */
    private function normalizeAddressComponents($address): array
    {
        // If address is nested in 'addr' (common in Surepass advanced), pull it out
        if (isset($address['addr']) && is_array($address['addr'])) {
            $address = $address['addr'];
        }

        return [
            'building_name' => $address['building_name'] ?? $address['bnm'] ?? null,
            'building_number' => $address['building_number'] ?? $address['bno'] ?? null,
            'floor_number' => $address['floor_number'] ?? $address['flno'] ?? null,
            'street' => $address['street'] ?? $address['st'] ?? null,
            'location' => $address['location'] ?? $address['loc'] ?? null,
            'district' => $address['district'] ?? $address['dst'] ?? null,
            'city' => $address['city'] ?? null, // Not always provided; left null if absent
            'state' => $address['state'] ?? $address['stcd'] ?? null,
            'pincode' => $address['pincode'] ?? $address['pncd'] ?? null,
        ];
    }
}
