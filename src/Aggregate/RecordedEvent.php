<?php

declare(strict_types=1);

namespace BusFactor\Aggregate;

use DateTimeImmutable;
use DateTimeInterface;

final class RecordedEvent
{
    private EventInterface $event;

    private int $version;

    private DateTimeInterface $recordTime;

    public function __construct(EventInterface $event, int $version)
    {
        $this->event = $event;
        $this->version = $version;
        $this->recordTime = new DateTimeImmutable();
    }

    public function getEvent(): EventInterface
    {
        return $this->event;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getRecordTime(): DateTimeInterface
    {
        return $this->recordTime;
    }

    public function withRecordTime(DateTimeInterface $recordTime): self
    {
        $clone = clone $this;
        $clone->recordTime = $recordTime;
        return $clone;
    }
}
