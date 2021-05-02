<?php

declare(strict_types=1);

namespace BusFactor\StreamPublishingInspection;

use BusFactor\EventBus\EventBusInterface;
use BusFactor\EventStore\Filter;
use BusFactor\EventStore\InspectorInterface;

final class Inspector implements InspectorInterface
{
    private EventBusInterface $eventBus;

    /** @var callable|null */
    private $before;

    /** @var callable|null */
    private $after;

    public function __construct(EventBusInterface $eventBus, ?callable $before = null, ?callable $after = null)
    {
        $this->eventBus = $eventBus;
        $this->before = $before;
        $this->after = $after;
    }

    public function getFilter(): Filter
    {
        return new Filter();
    }

    public function inspect(string $aggregateId, string $streamType, RecordedEvent $recordedEvent): void
    {
        $stream = new Stream($aggregateId, $streamType);
        $stream = $stream->withRecordedEvent($recordedEvent);
        if ($this->before) {
            $before = $this->before;
            $before($aggregateId, $recordedEvent);
        }
        $this->eventBus->publish($stream);
        if ($this->after) {
            $after = $this->after;
            $after($aggregateId, $recordedEvent);
        }
    }
}
