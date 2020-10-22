<?php

declare(strict_types=1);

namespace BusFactor\CommandDispatcher;

use RuntimeException;

trait HandleCommandTrait
{
    public function handle(object $command): void
    {
        $classParts = explode('\\', get_class($command));
        $method = 'handle' . end($classParts);

        if (method_exists($this, $method)) {
            $this->$method($command);
        } else {
            $message = sprintf('Function "%s" must be implemented in class %s', $method, get_class($this));
            throw new RuntimeException($message);
        }
    }
}
