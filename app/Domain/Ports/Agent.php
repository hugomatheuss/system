<?php

namespace App\Domain\Ports;

interface Agent
{
    /**
     * @param  array<string,mixed>  $metadata
     * @return array<string,mixed>
     */
    public function analyze(string $content, array $metadata = []): array;
}
