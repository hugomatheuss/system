<?php

namespace App\Infrastructure\Publishers;

use App\Infrastructure\Messaging\RabbitMQConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

final class RabbitMQPublisher
{
    private AMQPStreamConnection $connection;

    public function __construct(?AMQPStreamConnection $connection = null)
    {
        $this->connection = $connection ?? RabbitMQConnection::getConnection();
    }

    /**
     * @param  array<string,mixed>  $payload
     *
     * @throws \Exception
     */
    public function publish(string $exchange, string $routingKey, array $payload): void
    {
        $channel = $this->connection->channel();

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
            $channel->close();
        }
    }
}
