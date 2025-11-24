<?php

namespace App\Domain\Events;

final class UserRegisteredEvent
{
    public string $id;

    public string $email;

    public \DateTimeImmutable $occurredAt;

    public function __construct(string $id, string $email, ?\DateTimeImmutable $occurredAt = null)
    {
        $this->id = $id;
        $this->email = $email;
        $this->occurredAt = $occurredAt ?? new \DateTimeImmutable;
    }

    /**
     * Converte o evento em um array serializável.
     *
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'occurred_at' => $this->occurredAt->format(\DateTime::ATOM),
        ];
    }
}
