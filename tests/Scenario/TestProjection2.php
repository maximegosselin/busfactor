<?php

declare(strict_types=1);

namespace BusFactor\Test\Scenario;

use BusFactor\Projection\ProjectionInterface;

class TestProjection2 implements ProjectionInterface
{
    public function getId(): string
    {
        return 'projection2';
    }
}
