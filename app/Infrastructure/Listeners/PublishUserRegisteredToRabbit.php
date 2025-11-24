<?php

namespace App\Infrastructure\Listeners;

use App\Domain\Events\UserRegisteredEvent;
use App\Domain\Ports\UserRegisteredPublisher;

class PublishUserRegisteredToRabbit
{
    protected UserRegisteredPublisher $publisher;

    public function __construct(UserRegisteredPublisher $publisher)
    {
        $this->publisher = $publisher;
    }

    public function handle(UserRegisteredEvent $event): void
    {
        $this->publisher->publish($event);
    }
}
