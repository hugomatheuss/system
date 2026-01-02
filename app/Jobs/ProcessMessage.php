<?php

namespace App\Jobs;

use App\Domain\Ports\Agent;
use App\Models\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessMessage implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private Message $message)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(Agent $agent): void
    {
        Log::info("Processing message job: {$this->message->id}");

        try {
            $aiService->processMessage($this->message, [
                'system_prompt' => 'Analise a seguinte mensagem e forneça insights relevantes.',
                'temperature' => 0.7,
                'max_tokens' => 500,
            ]);

            event(new MessageProcessed($this->message));
        } catch (\Exception $e) {
            Log::error("Job failed for message {$this->message->id}: {$e->getMessage()}");
            $this->fail($e);
        }
    }
}
