<?php

declare(strict_types=1);

namespace BusFactor\Aggregate;

interface EventInterface
{
    public static function getRevision(): int;

    public function serialize(): array;

    public static function deserialize(array $data): self;
}
