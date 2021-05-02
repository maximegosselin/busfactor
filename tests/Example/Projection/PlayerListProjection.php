<?php

declare(strict_types=1);

namespace BusFactor\Test\Example\Projection;

use BusFactor\Projection\ProjectionInterface;

class PlayerListProjection implements ProjectionInterface
{
    /** @var string */
    public const ID = __CLASS__;

    private array $payload = [];

    public function getId(): string
    {
        return self::ID;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function withPlayer(string $id, int $number, string $name): self
    {
        $clone = clone $this;
        $clone->payload[$id] = [
            'number' => $number,
            'name' => $name,
        ];
        return $clone;
    }

    public function withPlayerName(string $id, string $name): self
    {
        if (!isset($this->payload[$id])) {
            return $this;
        }
        $clone = clone $this;
        $clone->payload[$id]['name'] = $name;
        return $clone;
    }
}
