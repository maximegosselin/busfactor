<?php

declare(strict_types=1);

namespace BusFactor\EventStore;

use BusFactor\Aggregate\RecordedEvent;

class TestInspector implements InspectorInterface
{
    /** @var RecordedEvent[] */
    private array $inspectedEvents = [];

    public function getFilter(): Filter
    {
        return new Filter();
    }

    public function inspect(string $streamId, string $streamType, RecordedEvent $envelope): void
    {
        $this->inspectedEvents[] = $envelope;
    }

    public function getInspectedEvents(): array
    {
        return $this->inspectedEvents;
    }
}
