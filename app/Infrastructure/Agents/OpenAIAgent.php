<?php

namespace App\Infrastructure\Agents;

use App\Domain\Ports\Agent;
use GuzzleHttp\Client;

class OpenAIAgent implements Agent
{
    private Client $http;

    private string $apiKey;

    private string $endpoint;

    public function __construct(?Client $http = null)
    {
        $this->http = $http ?? new Client;
        $this->apiKey = config('services.openai.api_key');
        $this->endpoint = config('services.openai.endpoint', 'https://api.openai.com/v1/chat/completions');
    }

    /**
     * {@inheritDoc}
     */
    public function analyze(string $content, array $metadata = []): array
    {
        $payload = [
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'user', 'content' => $content],
            ],
            'max_tokens' => 500,
        ];

        $response = $this->http->post($this->endpoint, [
            'headers' => [
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => $payload,
            'timeout' => 30,
        ]);

        $body = json_decode((string) $response->getBody(), true);

        return $body ?? ['error' => 'empty_response'];
    }
}
