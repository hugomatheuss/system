<?php

namespace App\Infrastructure\Messaging;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQConnection
{
    protected static ?AMQPStreamConnection $connection = null;

    /**
     * @throws \Exception
     */
    public static function getConnection(): AMQPStreamConnection
    {
        if (self::$connection instanceof AMQPStreamConnection) {
            return self::$connection;
        }

        $cfg = config('rabbitmq', []);

        $host = $cfg['host'] ?? null;
        $port = (int) ($cfg['port'] ?? 5672);
        $user = $cfg['user'] ?? 'guest';
        $password = $cfg['password'] ?? 'guest';
        $vhost = $cfg['vhost'] ?? '/';

        if (empty($host)) {
            throw new \RuntimeException('RabbitMQ host not configured. Defina `RABBITMQ_HOST` no .env ou em config/rabbitmq.php.');
        }

        try {
            self::$connection = new AMQPStreamConnection($host, $port, $user, $password, $vhost);
        } catch (\Throwable $e) {
            throw new \RuntimeException('Falha ao conectar ao RabbitMQ: '.$e->getMessage(), 0, $e);
        }

        return self::$connection;
    }
}
