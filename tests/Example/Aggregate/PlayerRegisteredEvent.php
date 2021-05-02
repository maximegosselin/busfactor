<?php

declare(strict_types=1);

namespace BusFactor\Test\Example\Aggregate;

use BusFactor\Aggregate\RevisionTrait;
use BusFactor\Aggregate\SerializationTrait;
use BusFactor\Aggregate\StreamEventInterface;

class PlayerRegisteredEvent implements StreamEventInterface
{
    use RevisionTrait;
    use SerializationTrait;

    public const REVISION = 1;

    private int $number;

    private string $name;

    public function __construct(int $number, string $name)
    {
        $this->number = $number;
        $this->name = $name;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
