<?php

declare(strict_types=1);

namespace BusFactor\EventStore;

use BusFactor\Aggregate\RecordedEvent;

interface InspectorInterface
{
    public function getFilter(): Filter;

    public function inspect(string $streamId, string $streamType, RecordedEvent $recordedEvent): void;
}
