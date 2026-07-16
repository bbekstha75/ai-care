<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class AssemblyAiTokenController extends Controller
{
    /**
     * Mint a temporary token for AssemblyAI streaming transcription.
     */
    public function __invoke(): JsonResponse
    {
        $apiKey = config('services.assemblyai.key');

        if (! $apiKey) {
            return response()->json(['error' => 'AssemblyAI API key is missing.'], 500);
        }

        // Request a temporary token valid for 8 minutes (480 seconds)
        $response = Http::withHeaders([
            'Authorization' => $apiKey, // No 'Bearer ' prefix required for AssemblyAI token endpoint
        ])->get('https://streaming.assemblyai.com/v3/token', [
            'expires_in_seconds' => 480,
        ]);

        if ($response->successful()) {
            return response()->json([
                'token' => $response->json('token'),
            ]);
        }

        return response()->json([
            'error' => 'Failed to generate AssemblyAI token.',
            'details' => $response->body(),
        ], $response->status());
    }
}
