<?php

declare(strict_types=1);

namespace BusFactor\CommandDispatcher;

interface DispatcherInterface
{
    public function dispatch(object $command): void;
}
