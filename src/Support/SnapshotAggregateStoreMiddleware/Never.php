<?php

declare(strict_types=1);

namespace BusFactor\Support\SnapshotAggregateStoreMiddleware;

use BusFactor\Aggregate\AggregateInterface;

class Never implements StrategyInterface
{
    public function mustSnapshot(AggregateInterface $aggregate): bool
    {
        return false;
    }

    public function mustLoad(): bool
    {
        return false;
    }
}
