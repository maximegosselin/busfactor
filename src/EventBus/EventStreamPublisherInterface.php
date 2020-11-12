<?php

declare(strict_types=1);

namespace BusFactor\EventBus;

use BusFactor\Aggregate\Stream;

interface EventStreamPublisherInterface
{
    public function publish(Stream $stream): void;
}
