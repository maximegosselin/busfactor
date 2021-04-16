<?php

declare(strict_types=1);

namespace BusFactor\Extra\PdoProjectionStore;

use BusFactor\Projection\ProjectionInterface;

class TestProjection implements ProjectionInterface
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
