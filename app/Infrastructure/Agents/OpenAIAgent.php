<?php

namespace App\Infrastructure\Agents;

use App\Domain\Ports\Agent;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class OpenAIAgent implements Agent
{
    private Client $client;
    private string $model;

    public function __construct(string $apiKey, string $model = 'gpt-4o-mini')
    {
        $this->client = \OpenAI::client($apiKey);
        $this->model = $model;
    }

    /**
     * {@inheritDoc}
     */
    public function analyze(string $content, array $metadata = []): array
    {
        try {
            $systemPrompt = $metadata['system_prompt'] ?? 'Você é um assistente inteligente que analisa mensagens e fornece insights úteis.';
            $temperature = $metadata['temperature'] ?? 0.7;
            $maxTokens = $metadata['max_tokens'] ?? 500;

            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt,
                    ],
                    [
                        'role' => 'user',
                        'content' => $content,
                    ],
                ],
                'temperature' => $temperature,
                'max_tokens' => $maxTokens,
            ]);

            return [
                'success' => true,
                'content' => $response->choices[0]->message->content,
                'model' => $this->model,
                'provider' => 'openai',
                'usage' => [
                    'prompt_tokens' => $response->usage->promptTokens,
                    'completion_tokens' => $response->usage->completionTokens,
                    'total_tokens' => $response->usage->totalTokens,
                ],
            ];
        } catch (\Exception $e) {
            Log::error('OpenAI error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => 'openai',
            ];
        }
    }

    public function supportsProvider(string $provider): bool
    {
        return strtolower($provider) === 'openai';
    }
}
