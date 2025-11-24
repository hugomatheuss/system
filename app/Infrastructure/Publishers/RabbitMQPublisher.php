<?php

namespace App\Infrastructure\Publishers;

use App\Infrastructure\Messaging\RabbitMQConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQPublisher
{
    /**
     * @param  array<string,mixed>  $payload
     *
     * @throws \Exception
     */
    public function publish(string $exchange, string $routingKey, array $payload): void
    {
        $connection = RabbitMQConnection::getConnection();
        $channel = $connection->channel();

        try {
            $channel->exchange_declare(
                $exchange,
                AMQPExchangeType::TOPIC,
                false,
                true,
                false
            );

            $body = json_encode($payload);
            if ($body === false) {
                throw new \InvalidArgumentException('Failed to JSON encode payload.');
            }

            $message = new AMQPMessage(
                $body,
                ['content_type' => 'application/json', 'delivery_mode' => 2]
            );

            $channel->basic_publish($message, $exchange, $routingKey);
        } finally {
            $connection->close();
        }
    }
}
