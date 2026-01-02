<?php

namespace App\Domain\Events;

use App\Models\Message;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageProcessed
{
    use Dispatchable, SerializesModels;

    public function __construct(public Message $message)
    {
    }
}
