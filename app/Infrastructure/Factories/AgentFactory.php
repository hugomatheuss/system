<?php

namespace App\Infrastructure\Factories;

use App\Domain\Ports\Agent;
use App\Infrastructure\Agents\OllamaAgent;
use App\Infrastructure\Agents\OpenAIAgent;
use InvalidArgumentException;

class AgentFactory
{
    /**
     * @param  array<string,mixed>  $config
     */
    public static function create(string $provider, array $config = []): Agent
    {
        return match ($provider) {
            'openai' => new OpenAIAgent(
                $config['api_key'] ?? config('services.openai.api_key'),
                $config['model'] ?? config('services.ai.model', 'gpt-4o-mini')
            ),
            'ollama' => new OllamaAgent(
                $config['base_url'] ?? config('services.ollama.base_url', 'http://ollama:11434'),
                $config['model'] ?? config('services.ai.model', 'llama2')
            ),
            default => throw new InvalidArgumentException("Unknown AI provider: {$provider}")
        };
    }
}
