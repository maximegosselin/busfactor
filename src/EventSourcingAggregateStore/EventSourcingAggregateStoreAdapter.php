<?php

declare(strict_types=1);

namespace BusFactor\EventSourcingAggregateStore;

use BusFactor\Aggregate\AggregateFactory;
use BusFactor\Aggregate\AggregateInterface;
use BusFactor\Aggregate\Stream;
use BusFactor\AggregateStore\AdapterInterface;
use BusFactor\AggregateStore\AggregateNotFoundException;
use BusFactor\EventBus\EventBusInterface;
use BusFactor\EventStore\EventStoreInterface;
use BusFactor\EventStore\StreamNotFoundException;
use InvalidArgumentException;
use RuntimeException;

final class EventSourcingAggregateStoreAdapter implements AdapterInterface
{
    private AggregateFactory $aggregateFactory;

    private EventStoreInterface $eventStore;

    private EventBusInterface $eventBus;

    public function __construct(
        AggregateFactory $aggregateFactory,
        EventStoreInterface $eventStore,
        EventBusInterface $eventBus
    ) {
        $this->aggregateFactory = $aggregateFactory;
        $this->eventStore = $eventStore;
        $this->eventBus = $eventBus;
    }

    public function find(string $aggregateId, string $aggregateType): AggregateInterface
    {
        if ($this->getExpectedAggregateType() !== $aggregateType) {
            throw new InvalidArgumentException('Unexpected aggregate type.');
        }
        try {
            $stream = $this->eventStore->fetch($aggregateId, $aggregateType);
        } catch (StreamNotFoundException $e) {
            throw AggregateNotFoundException::forAggregate($aggregateId, $aggregateType, $e);
        }
        $class = $this->aggregateFactory->getAggregateRootClass();
        /** @var AggregateInterface $aggregate */
        $aggregate = new $class($stream->getStreamId());
        $aggregate->replayStream($stream);

        return $aggregate;
    }

    public function has(string $aggregateId, string $aggregateType): bool
    {
        return $this->eventStore->streamExists($aggregateId, $aggregateType);
    }

    public function store(AggregateInterface $aggregate): void
    {
        $stream = new Stream($aggregate->getAggregateId(), $aggregate::getType());
        $recordedEvents = $aggregate->pullNewEvents();
        foreach ($recordedEvents as $recordedEvent) {
            $stream = $stream->withRecordedEvent($recordedEvent);
        }
        $this->eventStore->append($stream);
        $this->eventBus->publish($stream);
    }

    public function remove(string $aggregateId, string $aggregateType): void
    {
        throw new RuntimeException('Not implemented.');
    }

    public function purge(): void
    {
        throw new RuntimeException('Not implemented.');
    }

    private function getExpectedAggregateType(): string
    {
        /** @var AggregateInterface $class */
        $class = $this->aggregateFactory->getAggregateRootClass();
        return $class::getType();
    }
}
