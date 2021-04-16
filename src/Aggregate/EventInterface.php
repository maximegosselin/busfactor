<?php

declare(strict_types=1);

namespace BusFactor\Aggregate;

interface EventInterface
{
    public static function getRevision(): int;

    public static function deserialize(array $data): self;

    public function serialize(): array;
}
