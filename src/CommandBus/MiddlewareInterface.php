<?php

declare(strict_types=1);

namespace BusFactor\CommandBus;

interface MiddlewareInterface
{
    public function dispatch(object $command, CommandDispatcherInterface $next): void;
}
