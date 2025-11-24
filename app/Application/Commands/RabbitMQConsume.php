<?php

namespace App\Application\Commands;

use Illuminate\Console\Command;

class RabbitMQConsume extends Command
{
    protected $signature = 'rabbitmq:consume {consumer}';

    protected $description = 'Start a RabbitMQ consumer';

    public function handle(): void
    {
        $consumerName = $this->argument('consumer');

        $class = "App\\Infrastructure\\Consumers\\{$consumerName}";

        if (! class_exists($class)) {
            $this->error("Consumer {$consumerName} not found.");

            return;
        }

        (new $class)->consume();
    }
}
