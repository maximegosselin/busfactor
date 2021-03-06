<?php

declare(strict_types=1);

namespace BusFactor\Test\EventBus;

use BusFactor\Aggregate\Stream;

class TestMiddleware implements MiddlewareInterface
{
    private string $name;

    private array $output;

    public function __construct(string $name, array &$output)
    {
        $this->name = $name;
        $this->output = &$output;
    }

    public function publish(Stream $stream, EventStreamPublisherInterface $next): void
    {
        $this->output[] = 'before ' . $this->name;
        $next->publish($stream);
        $this->output[] = 'after ' . $this->name;
    }
}
