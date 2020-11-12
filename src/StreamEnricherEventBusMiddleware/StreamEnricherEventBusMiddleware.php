<?php

declare(strict_types=1);

namespace BusFactor\StreamEnricherEventBusMiddleware;

use BusFactor\Aggregate\Stream;
use BusFactor\EventBus\EventStreamPublisherInterface;
use BusFactor\EventBus\MiddlewareInterface;
use BusFactor\StreamEnricher\StreamEnricherInterface;

final class StreamEnricherEventBusMiddleware implements MiddlewareInterface
{
    private StreamEnricherInterface $enricher;

    public function __construct(StreamEnricherInterface $enricher)
    {
        $this->enricher = $enricher;
    }

    public function publish(Stream $stream, EventStreamPublisherInterface $next): void
    {
        $next->publish($this->enricher->enrich($stream));
    }
}
