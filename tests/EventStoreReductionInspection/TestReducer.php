<?php

declare(strict_types=1);

namespace BusFactor\Test\EventStore;

use BusFactor\Aggregate\RecordedEvent;
use BusFactor\EventStoreReductionInspection\ReductionInspectorInterface;

class TestReducer implements ReductionInspectorInterface
{
    private int $eventCount = 0;

    public function getFilter(): Filter
    {
        return new Filter();
    }

    public function inspect(string $streamId, string $streamType, RecordedEvent $envelope): void
    {
        $this->eventCount++;
    }

    /** @return mixed */
    public function getResult()
    {
        return $this->eventCount;
    }

    public function reset(): void
    {
        $this->eventCount = 0;
    }
}
