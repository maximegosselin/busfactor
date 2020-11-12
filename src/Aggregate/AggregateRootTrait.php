<?php

declare(strict_types=1);

namespace BusFactor\Aggregate;

trait AggregateRootTrait
{
    private string $aggregateRootTrait_aggregateId;

    private int $aggregateRootTrait_version = 0;

    /** @var RecordedEvent[] */
    private array $aggregateRootTrait_newEvents = [];

    public function __construct(string $aggregateId)
    {
        $this->aggregateRootTrait_aggregateId = $aggregateId;
    }

    private function __handle(RecordedEvent $recordedEvent): void
    {
        $parts = explode('\\', get_class($recordedEvent->getEvent()));
        $method = 'apply' . end($parts);
        if (method_exists($this, $method)) {
            $this->$method($recordedEvent->getEvent(), $recordedEvent);
        }
    }

    public function getAggregateId(): string
    {
        return $this->aggregateRootTrait_aggregateId;
    }

    public function getVersion(): int
    {
        return $this->aggregateRootTrait_version;
    }

    public function pullNewEvents(): array
    {
        $recordedEvents = $this->peekNewEvents();
        $this->aggregateRootTrait_newEvents = [];
        return $recordedEvents;
    }

    public function peekNewEvents(): array
    {
        return $this->aggregateRootTrait_newEvents;
    }

    private function apply(EventInterface $event): void
    {
        $this->aggregateRootTrait_version++;
        $recordedEvent = new RecordedEvent($event, $this->aggregateRootTrait_version);
        $this->__handle($recordedEvent);
        $this->aggregateRootTrait_newEvents[] = $recordedEvent;
    }
}
