<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key', env('GEMINI_API_KEY'));
    }

    public function generateCopy(string $prompt, string $tone = 'professional')
    {
        if (empty($this->apiKey)) {
            return "Error: Gemini API key is not configured.";
        }

        $systemPrompt = "You are an expert marketing copywriter. Tone: {$tone}. Provide highly engaging, conversion-optimized marketing copy. Do not include introductory text, just the copy.";
        
        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $systemPrompt . "\n\nClient Request: " . $prompt]
                    ]
                ]
            ]
        ];

        try {
            $response = Http::timeout(5)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '?key=' . $this->apiKey, $payload);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    return $data['candidates'][0]['content']['parts'][0]['text'];
                }
                return "Failed to parse Gemini response.";
            }

            Log::error('Gemini API Error: ' . $response->body());
            return "Error connecting to Gemini AI.";
        } catch (\Exception $e) {
            Log::error('Gemini Exception: ' . $e->getMessage());
            return "An exception occurred while contacting the AI.";
        }
    }

    public function generateContent(string $systemPrompt, string $userPrompt)
    {
        if (empty($this->apiKey)) {
            return "Error: Gemini API key is not configured.";
        }

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $systemPrompt . "\n\nUser Input: " . $userPrompt]
                    ]
                ]
            ]
        ];

        try {
            $response = Http::timeout(5)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '?key=' . $this->apiKey, $payload);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    return $data['candidates'][0]['content']['parts'][0]['text'];
                }
                return "Failed to parse Gemini response.";
            }

            Log::error('Gemini API Error: ' . $response->body());
            return "Error connecting to Gemini AI.";
        } catch (\Exception $e) {
            Log::error('Gemini Exception: ' . $e->getMessage());
            return "An exception occurred while contacting the AI.";
        }
    }
}
