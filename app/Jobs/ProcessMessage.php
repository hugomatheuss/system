<?php

namespace App\Jobs;

use App\Domain\Ports\Agent;
use App\Models\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessMessage implements ShouldQueue
{
    use Queueable;

    private string $messageId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $messageId)
    {
        $this->messageId = $messageId;
    }

    /**
     * Execute the job.
     */
    public function handle(Agent $agent): void
    {
        $message = Message::find($this->messageId);
        if (! $message) {
            return;
        }

        $result = $agent->analyze($message->content, $message->metadata ?? []);

        $message->ai_result = $result;
        $message->processed_at = now();
        $message->save();
    }
}
