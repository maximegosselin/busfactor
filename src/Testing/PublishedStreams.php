<?php

declare(strict_types=1);

namespace BusFactor\Testing;

use BusFactor\Aggregate\RecordedEvent;
use BusFactor\Aggregate\Stream;
use Countable;

final class PublishedStreams implements Countable
{
    /** @var Stream[] */
    private array $streams;

    public function __construct(array $streams)
    {
        $this->streams = $streams;
    }

    public function count(): int
    {
        return count($this->streams);
    }

    /** @return Stream[] */
    public function getAll(): array
    {
        return $this->streams;
    }

    /** @return RecordedEvent[] */
    public function getAllOf(string $eventClass): array
    {
        $recordedEvents = [];
        foreach ($this->streams as $stream) {
            foreach ($stream->getRecordedEvents() as $recordedEvent) {
                if (get_class($recordedEvent->getEvent()) === $eventClass) {
                    $recordedEvents[] = $recordedEvent;
                }
            }
        }
        return $recordedEvents;
    }
}
