<?php

return [
    'host' => env('RABBITMQ_HOST', env('RABBITMQ_HOSTNAME', null)),
    'port' => (int) env('RABBITMQ_PORT', null),
    'user' => env('RABBITMQ_LOGIN', env('RABBITMQ_USER', null)),
    'password' => env('RABBITMQ_PASSWORD', env('RABBITMQ_PASS', null)),
    'vhost' => env('RABBITMQ_VHOST', '/'),
    'exchange_declare' => filter_var(env('RABBITMQ_EXCHANGE_DECLARE', true), FILTER_VALIDATE_BOOLEAN),
    'exchange_type' => env('RABBITMQ_EXCHANGE_TYPE', 'direct'),
    'ssl' => filter_var(env('RABBITMQ_SSL', false), FILTER_VALIDATE_BOOLEAN),
];
