<?php

declare(strict_types=1);

namespace BusFactor\CommandDispatcher;

final class Router implements DispatcherInterface
{
    /** @var HandlerInterface[] */
    private array $map;

    public function dispatch(object $command): void
    {
        if (is_object($command)) {
            $this->route($command);
        }
    }

    public function registerHandler(string $commandClass, HandlerInterface $handler): void
    {
        $this->map[$commandClass] = $handler;
    }

    private function route(object $command): void
    {
        $name = get_class($command);
        if (isset($this->map[$name])) {
            $this->map[$name]->handle($command);
        }
    }
}
