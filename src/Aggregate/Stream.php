<?php

declare(strict_types=1);

namespace BusFactor\Aggregate;

final class Stream
{
    private string $streamId;

    private string $streamType;

    /** @var RecordedEvent[] */
    private array $recordedEvents = [];

    private int $lowestVersion = 0;

    private int $highestVersion = 0;

    public function __construct(string $streamId, string $streamType)
    {
        $this->streamId = $streamId;
        $this->streamType = $streamType;
    }

    public function getStreamId(): string
    {
        return $this->streamId;
    }

    public function getStreamType(): string
    {
        return $this->streamType;
    }

    public function withRecordedEvent(RecordedEvent $recordedEvent): self
    {
        $clone = clone $this;
        $clone->recordedEvents[] = $recordedEvent;
        usort($clone->recordedEvents, function (RecordedEvent $a, RecordedEvent $b) {
            return $a->getVersion() <=> $b->getVersion();
        });

        if ($clone->highestVersion == 0) {
            $clone->highestVersion = $recordedEvent->getVersion();
        } elseif ($recordedEvent->getVersion() > $clone->highestVersion) {
            $clone->highestVersion = $recordedEvent->getVersion();
        }
        if ($clone->lowestVersion == 0) {
            $clone->lowestVersion = $recordedEvent->getVersion();
        } elseif ($recordedEvent->getVersion() < $clone->lowestVersion) {
            $clone->lowestVersion = $recordedEvent->getVersion();
        }
        return $clone;
    }

    /** @return RecordedEvent[] */
    public function getRecordedEvents(): array
    {
        return $this->recordedEvents;
    }

    public function getLowestVersion(): int
    {
        return $this->lowestVersion;
    }

    public function getHighestVersion(): int
    {
        return $this->highestVersion;
    }
}
