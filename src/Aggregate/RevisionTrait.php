<?php

declare(strict_types=1);

namespace BusFactor\Aggregate;

trait RevisionTrait
{
    public static function getRevision(): int
    {
        return self::REVISION;
    }
}
