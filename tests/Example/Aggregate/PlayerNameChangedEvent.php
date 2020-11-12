<?php

declare(strict_types=1);

namespace BusFactor\Example\Aggregate;

use BusFactor\Aggregate\RevisionTrait;
use BusFactor\Aggregate\SerializationTrait;
use BusFactor\Aggregate\StreamEventInterface;

class PlayerNameChangedEvent implements StreamEventInterface
{
    use RevisionTrait;
    use SerializationTrait;

    public const REVISION = 1;

    private string $previous;

    private string $new;

    public function __construct(string $previous, string $new)
    {
        $this->previous = $previous;
        $this->new = $new;
    }

    public function getPrevious(): string
    {
        return $this->previous;
    }

    public function getNew(): string
    {
        return $this->new;
    }
}
