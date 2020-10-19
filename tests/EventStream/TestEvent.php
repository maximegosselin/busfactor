<?php

declare(strict_types=1);

namespace BusFactor\EventStream;

class TestEvent implements StreamEventInterface
{
    use RevisionTrait;
    use SerializationTrait;

    public const REVISION = 1;

    private string $string;

    private int $integer;

    private array $array;

    public function __construct(string $string, int $integer, array $array)
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
