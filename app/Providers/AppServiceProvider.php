<?php

namespace App\Providers;

use App\Application\Commands\RabbitMQConsume;
use App\Domain\Ports\Agent;
use App\Domain\Ports\UserRegisteredPublisher;
use App\Infrastructure\Agents\OpenAIAgent;
use App\Infrastructure\Publishers\RabbitMQDomainPublisher;
use App\Infrastructure\Publishers\RabbitMQPublisher;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(RabbitMQPublisher::class, function ($app) {
            return new RabbitMQPublisher;
        });

        $this->app->bind(
            UserRegisteredPublisher::class,
            RabbitMQDomainPublisher::class,
        );

        $this->app->singleton(Agent::class, function ($app) {;
            $apiKey = config('services.openai.api_key');
            $endpoint = config('services.openai.endpoint');

            return new OpenAIAgent($apiKey, $endpoint);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                RabbitMQConsume::class,
            ]);
        }
    }
}
