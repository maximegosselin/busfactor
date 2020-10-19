<?php

declare(strict_types=1);

namespace BusFactor\EventBus;

use BusFactor\EventStream\Stream;

class EventStreamPublisherDelegator implements EventStreamPublisherInterface
{
    private MiddlewareInterface $middleware;

    private ?EventStreamPublisherInterface $next;

    public function __construct(MiddlewareInterface $middleware, ?EventStreamPublisherInterface $next = null)
    {
        $this->middleware = $middleware;
        $this->next = $next;
    }

    public function publish(Stream $stream): void
    {
        $this->middleware->publish($stream, $this->next);
    }
}
