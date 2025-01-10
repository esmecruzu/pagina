<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DialogflowService
{
    protected $apiUrl = 'https://dialogflow.googleapis.com/v2/projects';
    protected $projectId;
    protected $apiKey;

    public function __construct()
    {
        $this->projectId = config('dialogflow.project_id');
        $this->apiKey = config('dialogflow.api_key');
    }

    public function detectIntent(string $sessionId, string $message, string $languageCode = 'es')
    {
        $url = "{$this->apiUrl}/{$this->projectId}/agent/sessions/{$sessionId}:detectIntent";

        $response = Http::post($url, [
            'queryInput' => [
                'text' => [
                    'text' => $message,
                    'languageCode' => $languageCode,
                ],
            ],
        ])->withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ]);

        return $response->json();
    }
}
