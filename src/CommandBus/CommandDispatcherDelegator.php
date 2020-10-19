<?php

declare(strict_types=1);

namespace BusFactor\CommandBus;

class CommandDispatcherDelegator implements CommandDispatcherInterface
{
    private MiddlewareInterface $middleware;

    private ?CommandDispatcherInterface $next;

    public function __construct(MiddlewareInterface $middleware, ?CommandDispatcherInterface $next = null)
    {
        $this->middleware = $middleware;
        $this->next = $next;
    }

    public function dispatch(object $command): void
    {
        $this->middleware->dispatch($command, $this->next);
    }
}
