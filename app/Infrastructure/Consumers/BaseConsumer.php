<?php

namespace App\Infrastructure\Consumers;

use App\Infrastructure\Messaging\RabbitMQConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;

abstract class BaseConsumer
{
    abstract protected function queue(): string;

    abstract protected function exchange(): string;

    abstract protected function routingKey(): string;

    /**
     * @param  array<string,mixed>  $data
     */
    abstract protected function handle(array $data): void;

    public function consume(): void
    {
        $connection = RabbitMQConnection::getConnection();
        $channel = $connection->channel();

        $channel->basic_qos(0, 1, false);

        $channel->exchange_declare(
            $this->exchange(),
            AMQPExchangeType::TOPIC,
            false,
            true,
            false
        );

        $channel->queue_declare(
            $this->queue(),
            false,
            true,
            false,
            false
        );

        $channel->queue_bind(
            $this->queue(),
            $this->exchange(),
            $this->routingKey()
        );

        $callback = function ($msg) {
            try {
                $data = json_decode($msg->body, true);
                if (! is_array($data)) {
                    throw new \RuntimeException('Payload inválido no corpo da mensagem.');
                }

                $this->handle($data);

                $msg->ack();
            } catch (\Throwable $e) {
                if (function_exists('logger')) {
                    logger()->error('Erro ao processar mensagem RabbitMQ', [
                        'queue' => $this->queue(),
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }

                $redelivered = $msg->delivery_info['redelivered'] ?? false;
                if ($redelivered) {
                    $msg->nack(false, false);
                } else {
                    $msg->nack(false, true);
                }
            }
        };

        $channel->basic_consume(
            $this->queue(),
            '',
            false,
            false,
            false,
            false,
            $callback
        );

        echo " [*] Waiting for messages on {$this->queue()}...\n";

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
    }
}
