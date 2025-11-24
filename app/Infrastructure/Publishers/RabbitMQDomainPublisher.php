<?php

namespace App\Infrastructure\Publishers;

use App\Domain\Events\UserRegisteredEvent;
use App\Domain\Ports\UserRegisteredPublisher as UserRegisteredPublisherPort;

final class RabbitMQDomainPublisher implements UserRegisteredPublisherPort
{
    private RabbitMQPublisher $publisher;

    public function __construct(RabbitMQPublisher $publisher)
    {
        $this->publisher = $publisher;
    }

    public function publish(UserRegisteredEvent $event): void
    {
        /** @var array<string,mixed> $payload */
        $payload = $event->toArray();
        $this->publisher->publish('users.events', 'user.registered', $payload);
    }
}
