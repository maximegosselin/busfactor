<?php

declare(strict_types=1);

namespace BusFactor\Support\SnapshotAggregateStoreMiddleware;

use BusFactor\Aggregate\AggregateInterface;
use BusFactor\AggregateStore\AggregateNotFoundException;
use BusFactor\AggregateStore\AggregateStoreInterface;
use BusFactor\AggregateStore\MiddlewareInterface;
use BusFactor\EventStore\EventStoreInterface;
use BusFactor\EventStore\StreamNotFoundException;
use RuntimeException;

final class SnapshotAggregateStoreMiddleware implements MiddlewareInterface
{
    private AggregateStoreInterface $snapshots;

    private EventStoreInterface $eventStore;

    private StrategyInterface $strategy;

    public function __construct(
        AggregateStoreInterface $snapshots,
        EventStoreInterface $eventStore,
        StrategyInterface $strategy
    ) {
        $this->snapshots = $snapshots;
        $this->eventStore = $eventStore;
        $this->strategy = $strategy;
    }

    public function find(string $aggregateId, string $aggregateType, AggregateStoreInterface $next): AggregateInterface
    {
        if (!$this->strategy->mustLoad() || !$this->snapshots->has($aggregateId, $aggregateType)) {
            return $next->find($aggregateId, $aggregateType);
        }
        /** @var AggregateInterface $aggregate */
        $aggregate = $this->snapshots->find($aggregateId, $aggregateType);
        if (!$aggregate instanceof AggregateInterface) {
            throw new RuntimeException('Aggregate must implement ' . AggregateInterface::class);
        }
        $fromVersion = $aggregate->getVersion() + 1;
        try {
            $stream = $this->eventStore->fetch($aggregateId, $aggregateType, $fromVersion);
        } catch (StreamNotFoundException $e) {
            throw AggregateNotFoundException::forAggregate($aggregateId, $aggregateType, $e);
        }
        $aggregate->replayStream($stream);

        return $aggregate;
    }

    public function has(string $aggregateId, string $aggregateType, AggregateStoreInterface $next): bool
    {
        return $next->has($aggregateId, $aggregateType);
    }

    public function store(AggregateInterface $aggregate, AggregateStoreInterface $next): void
    {
        if (($aggregate instanceof AggregateInterface) && $this->strategy->mustSnapshot($aggregate)) {
            $clone = clone $aggregate;
            $clone->pullNewEvents();
            $this->snapshots->store($clone);
        }
        $next->store($aggregate);
    }

    public function remove(string $aggregateId, string $aggregateType, AggregateStoreInterface $next): void
    {
        $this->snapshots->remove($aggregateId, $aggregateType);
        $next->remove($aggregateId, $aggregateType);
    }

    public function purge(AggregateStoreInterface $next): void
    {
        $this->snapshots->purge();
        $next->purge();
    }
}
