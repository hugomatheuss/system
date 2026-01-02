<?php

namespace App\Application\Services;

use App\Domain\Ports\Agent;
use App\Infrastructure\Factories\AgentFactory;
use App\Models\Message;
use Illuminate\Support\Facades\Log;

class AIProcessingService
{
    private Agent $agent;

    public function __construct()
    {
        $provider = config('services.ai.provider', 'openai');
        $this->agent = AgentFactory::create($provider);
    }


    /**
     * @param  array<string,mixed>  $metadata
     * @return array<string,mixed>
     */
    public function processMessage(Message $message, array $metadata = []): array
    {
        try {
            Log::info("Processing message {$message->id}");

            $result = $this->agent->analyze($message->content, $metadata);

            if ($result['success']) {
                $message->update([
                    'processed_content' => $result['content'],
                    'processed_at' => now(),
                    'status' => 'processed',
                    'metadata' => $result,
                ]);

                Log::info("Message {$message->id} processed successfully");
            } else {
                $message->update(['status' => 'failed']);
                Log::error("Message {$message->id} processing failed: {$result['error']}");
            }

            return $result;
        } catch (\Exception $e) {
            Log::error("Exception processing message {$message->id}: {$e->getMessage()}");

            $message->update(['status' => 'failed']);

            throw $e;
        }
    }
}
