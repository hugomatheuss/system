<?php

namespace App\Providers;

use App\Domain\Events\UserRegisteredEvent;
use App\Domain\Ports\UserRegisteredPublisher as UserRegisteredPublisherPort;
use App\Infrastructure\Listeners\PublishUserRegisteredToRabbit;
use App\Infrastructure\Publishers\RabbitMQPublisher;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UserRegisteredEvent::class => [
            PublishUserRegisteredToRabbit::class,
        ],
    ];

    public function register(): void
    {
        $this->app->bind(UserRegisteredPublisherPort::class, function ($app) {
            return new RabbitMQPublisher($app->make(RabbitMQPublisher::class));
        });
    }
}
