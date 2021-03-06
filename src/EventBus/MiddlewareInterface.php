<?php

declare(strict_types=1);

namespace BusFactor\EventBus;

use BusFactor\Aggregate\Stream;

interface MiddlewareInterface
{
    public function publish(Stream $stream, EventStreamPublisherInterface $next): void;
}
