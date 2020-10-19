<?php

declare(strict_types=1);

namespace BusFactor\MemcachedProjectionStore;

use BusFactor\Projection\ProjectionInterface;
use BusFactor\ProjectionStore\InMemoryProjectionStoreAdapter;
use BusFactor\ProjectionStore\UnitOfWork;
use Generator;

class MemcachedProjectionStoreAdapterMock extends MemcachedProjectionStoreAdapter
{
    private InMemoryProjectionStoreAdapter $adapter;

    public function __construct(callable $resolver, string $namespace = 'projection-store')
    {
        parent::__construct($resolver, $namespace);
        $this->adapter = new InMemoryProjectionStoreAdapter();
    }

    public function find(string $id, string $class): ProjectionInterface
    {
        return $this->adapter->find($id, $class);
    }

    public function findBy(string $class): Generator
    {
        yield $this->adapter->findBy($class);
    }

    public function has(string $id, string $class): bool
    {
        return $this->adapter->has($id, $class);
    }

    public function commit(UnitOfWork $unit): void
    {
        $this->adapter->commit($unit);
    }

    public function purge(): void
    {
        $this->adapter->purge();
    }
}
