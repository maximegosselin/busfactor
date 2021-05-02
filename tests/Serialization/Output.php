<?php

declare(strict_types=1);

namespace BusFactor\Test\ObjectSerializer;

class Output
{
    /** @var string[] */
    private array $messages = [];

    public function write(string $message): void
    {
        $this->messages[] = $message;
    }

    public function read(): array
    {
        return $this->messages;
    }

    public function clear(): void
    {
        $this->messages = [];
    }
}
