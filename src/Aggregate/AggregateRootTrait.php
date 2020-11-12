<?php

declare(strict_types=1);

namespace BusFactor\Aggregate;

trait AggregateRootTrait
{
    private string $_aggregateId;

    private int $_version = 0;

    /** @var RecordedEvent[] */
    private array $_newEvents = [];

    public function __construct(string $aggregateId)
    {
        $this->_aggregateId = $aggregateId;
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
        return $this->_aggregateId;
    }

    public function getVersion(): int
    {
        return $this->_version;
    }

    public function pullNewEvents(): array
    {
        $recordedEvents = $this->peekNewEvents();
        $this->_newEvents = [];
        return $recordedEvents;
    }

    public function peekNewEvents(): array
    {
        return $this->_newEvents;
    }

    private function apply(EventInterface $event): void
    {
        $this->_version++;
        $recordedEvent = new RecordedEvent($event, $this->_version);
        $this->__handle($recordedEvent);
        $this->_newEvents[] = $recordedEvent;
    }
}
