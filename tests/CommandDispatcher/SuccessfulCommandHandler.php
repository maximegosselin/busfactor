<?php

declare(strict_types=1);

namespace BusFactor\Test\CommandDispatcher;

class SuccessfulCommandHandler implements HandlerInterface
{
    use HandleCommandTrait;

    public static function getHandledCommandClasses(): array
    {
        return [
            TestCommand::class,
        ];
    }

    private function handleTestCommand(TestCommand $command): void
    {
    }
}
