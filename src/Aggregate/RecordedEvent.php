<?php

declare(strict_types=1);

namespace BusFactor\Aggregate;

use DateTimeImmutable;
use DateTimeInterface;

final class RecordedEvent
{
    private EventInterface $event;

    private int $version;

    private Metadata $metadata;

    private DateTimeInterface $recordTime;

    private function __construct(
        EventInterface $event,
        int $version,
        Metadata $metadata,
        DateTimeInterface $recordTime
    ) {
        $this->event = $event;
        $this->version = $version;
        $this->metadata = $metadata;
        $this->recordTime = clone $recordTime;
    }

    public static function create(
        EventInterface $event,
        int $version,
        Metadata $metadata,
        DateTimeImmutable $recordTime
    ): self {
        return new static($event, $version, $metadata, $recordTime);
    }

    public static function createNow(EventInterface $event, Metadata $metadata, int $version): self
    {
        return new static($event, $version, $metadata, new DateTimeImmutable());
    }

    public function getEvent(): EventInterface
    {
        return $this->event;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getMetadata(): Metadata
    {
        return $this->metadata;
    }

    public function getRecordTime(): DateTimeInterface
    {
        return clone $this->recordTime;
    }

    public function withMetadata(Metadata $metadata): self
    {
        $clone = clone $this;
        $clone->metadata = $metadata;
        return $clone;
    }
}
