<?php

declare(strict_types=1);

namespace BusFactor\CommandBus;

final class Router implements CommandDispatcherInterface
{
    /** @var CommandHandlerInterface[] */
    private array $map;

    public function dispatch(object $command): void
    {
        if (is_object($command)) {
            $this->route($command);
        }
    }

    public function registerHandler(string $commandClass, CommandHandlerInterface $handler): void
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
