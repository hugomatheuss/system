<?php

namespace App\Infrastructure\Consumers;

use App\Jobs\HandleUserRegistered;

class UserRegisteredConsumer extends BaseConsumer
{
    protected function queue(): string
    {
        return 'users.registered.q';
    }

    protected function exchange(): string
    {
        return 'users.events';
    }

    protected function routingKey(): string
    {
        return 'user.registered';
    }

    /**
     * {@inheritDoc}
     */
    protected function handle(array $data): void
    {
        HandleUserRegistered::dispatch($data);
    }
}
