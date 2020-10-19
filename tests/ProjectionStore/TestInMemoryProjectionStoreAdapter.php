<?php

declare(strict_types=1);

namespace BusFactor\ProjectionStore;

class TestInMemoryProjectionStoreAdapter extends InMemoryProjectionStoreAdapter
{
    private ?UnitOfWork $unit = null;

    public function commit(UnitOfWork $unit): void
    {
        parent::commit($unit);
        $this->unit = $unit;
    }

    public function getCommitedUnitOfWork(): ?UnitOfWork
    {
        return $this->unit;
    }
}
