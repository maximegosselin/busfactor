<?php

declare(strict_types=1);

namespace BusFactor\CommandDispatcher;

class FailingCommandHandler implements HandlerInterface
{
    use HandleCommandTrait;

    public static function getHandledCommandClasses(): array
    {
        return [];
    }
}
