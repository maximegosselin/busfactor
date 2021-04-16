<?php

declare(strict_types=1);

namespace BusFactor\Support\StreamPublishingInspection;

use BusFactor\Aggregate\RevisionTrait;
use BusFactor\Aggregate\SerializationTrait;
use BusFactor\Aggregate\StreamEventInterface;

class TestEvent implements StreamEventInterface
{
    use RevisionTrait;
    use SerializationTrait;

    public const REVISION = 1;
}
