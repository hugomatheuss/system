<?php

namespace App\Domain\Ports;

use App\Domain\Events\UserRegisteredEvent;

interface UserRegisteredPublisher
{
    public function publish(UserRegisteredEvent $event): void;
}
