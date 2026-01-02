<?php

namespace App\Infrastructure\Agents;

use App\Domain\Ports\Agent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaAgent implements Agent
{
    private string $baseUrl;
    private string $model;

    public function __construct(string $baseUrl = 'http://ollama:11434', string $model = 'llama2')
    {
        $this->baseUrl = $baseUrl;
        $this->model = $model;
    }

    /**
     * @param  array<string,mixed>  $metadata
     * @return array<string,mixed>
     */
    public function analyze(string $content, array $metadata = []): array
    {
        try {
            $temperature = $metadata['temperature'] ?? 0.7;
            $topK = $metadata['top_k'] ?? 40;
            $topP = $metadata['top_p'] ?? 0.9;

            $response = Http::timeout(120)
                ->post("{$this->baseUrl}/api/generate", [
                    'model' => $this->model,
                    'prompt' => $content,
                    'stream' => false,
                    'temperature' => $temperature,
                    'top_k' => $topK,
                    'top_p' => $topP,
                ])
                ->json();

            if (!isset($response['response'])) {
                throw new \Exception('Invalid response from Ollama');
            }

            return [
                'success' => true,
                'content' => $response['response'],
                'model' => $this->model,
                'provider' => 'ollama',
                'metadata' => [
                    'done' => $response['done'] ?? false,
                    'context' => $response['context'] ?? [],
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Ollama error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => 'ollama',
            ];
        }
    }

    public function supportsProvider(string $provider): bool
    {
        return $provider === 'ollama';
    }
}
