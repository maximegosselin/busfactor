<?php

declare(strict_types=1);

namespace BusFactor\CommandDispatcher;

interface HandlerInterface
{
    public function handle(object $command): void;
}
