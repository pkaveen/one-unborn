<?php

namespace App\Http\Controllers;

use App\Models\KnownPincode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PincodeLookupController extends Controller
{
    public function lookup(Request $request)
    {
        try {
            Log::info('Pincode lookup request received', ['data' => $request->all()]);
            
            $v = Validator::make($request->all(), [
                'pincode' => ['required', 'digits:6'],
            ]);

            if ($v->fails()) {
                Log::warning('Pincode validation failed', ['errors' => $v->errors()]);
                return response()->json(['error' => 'Invalid pincode. Provide a 6-digit number.'], 422);
            }

            $pincode = $request->input('pincode');
            Log::info('Processing pincode', ['pincode' => $pincode]);

            // Check if already stored in DB
            $existing = KnownPincode::where('pincode', $pincode)->first();
            if ($existing) {
                Log::info('Pincode found in database', ['pincode' => $pincode]);
                return response()->json([
                    'source' => 'db',
                    'pincode' => $existing->pincode,
                    'state' => $existing->state,
                    'district' => $existing->district,
                    'post_office' => $existing->post_office,
                ]);
            }

            Log::info('Fetching from external API', ['pincode' => $pincode]);
            // Fetch from public API with more robust options
            $response = Http::withOptions([
                'verify' => false,
                'timeout' => 30,
                'connect_timeout' => 10,
                'allow_redirects' => [
                    'max' => 3,
                    'strict' => true,
                    'referer' => true,
                    'protocols' => ['http', 'https']
                ],
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                ]
            ])->get("http://api.postalpincode.in/pincode/{$pincode}");

            if (! $response->ok()) {
                Log::error('External API failed', ['status' => $response->status(), 'pincode' => $pincode]);
                return response()->json(['error' => 'External API failed'], 502);
            }

            $data = $response->json();
            Log::info('External API response', ['data' => $data]);

            if (($data[0]['Status'] ?? '') !== 'Success') {
                Log::warning('Pincode not found in external API', ['pincode' => $pincode, 'response' => $data]);
                return response()->json(['error' => 'Pincode not found'], 404);
            }

            $po = $data[0]['PostOffice'][0] ?? null;

            $record = KnownPincode::create([
                'pincode' => $pincode,
                'state' => $po['State'] ?? null,
                'district' => $po['District'] ?? null,
                'post_office' => $po['Name'] ?? null,
            ]);

            Log::info('Pincode saved to database', ['pincode' => $pincode, 'record' => $record->toArray()]);

            return response()->json([
                'source' => 'api',
                'pincode' => $record->pincode,
                'state' => $record->state,
                'district' => $record->district,
                'post_office' => $record->post_office,
            ]);
        } catch (\Exception $e) {
            Log::error('Pincode lookup exception', [
                'error' => $e->getMessage(), 
                'trace' => $e->getTraceAsString(),
                'pincode' => $request->input('pincode')
            ]);
            return response()->json(['error' => 'Internal server error: ' . $e->getMessage()], 500);
        }
    }

}
