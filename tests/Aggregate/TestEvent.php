<?php

declare(strict_types=1);

namespace BusFactor\Test\Aggregate;

use BusFactor\Aggregate\EventInterface;
use BusFactor\Aggregate\RevisionTrait;
use BusFactor\Aggregate\SerializationTrait;

class TestEvent implements EventInterface
{
    use RevisionTrait;
    use SerializationTrait;

    public const REVISION = 1;

    private string $string;

    private int $integer;

    private array $array;

    public function __construct(string $string = '', int $integer = 0, array $array = [])
    {
        $this->string = $string;
        $this->integer = $integer;
        $this->array = $array;
    }

    public function getString(): string
    {
        return $this->string;
    }

    public function getInteger(): int
    {
        return $this->integer;
    }

    public function getArray(): array
    {
        return $this->array;
    }
}
