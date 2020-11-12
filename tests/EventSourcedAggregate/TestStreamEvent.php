<?php

declare(strict_types=1);

namespace BusFactor\EventSourcedAggregate;

use BusFactor\Aggregate\RevisionTrait;
use BusFactor\Aggregate\SerializationTrait;
use BusFactor\Aggregate\StreamEventInterface;

class TestStreamEvent implements StreamEventInterface
{
    use SerializationTrait;
    use RevisionTrait;

    public const REVISION = 1;

    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
