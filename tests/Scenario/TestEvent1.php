<?php

declare(strict_types=1);

namespace BusFactor\Scenario;

use BusFactor\Aggregate\RevisionTrait;
use BusFactor\Aggregate\SerializationTrait;
use BusFactor\Aggregate\StreamEventInterface;

class TestEvent1 implements StreamEventInterface
{
    use RevisionTrait;
    use SerializationTrait;

    public const REVISION = 1;
}
