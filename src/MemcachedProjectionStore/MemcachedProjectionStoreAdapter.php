<?php

declare(strict_types=1);

namespace BusFactor\MemcachedProjectionStore;

use BusFactor\Projection\ProjectionInterface;
use BusFactor\ProjectionStore\AdapterInterface;
use BusFactor\ProjectionStore\ProjectionNotFoundException;
use BusFactor\ProjectionStore\UnitOfWork;
use Generator;
use Memcached;
use RuntimeException;

class MemcachedProjectionStoreAdapter implements AdapterInterface
{
    /** @var callable */
    private $resolver;

    private ?Memcached $memcached = null;

    private string $namespace;

    public function __construct(callable $resolver, string $namespace = 'projection-store')
    {
        $this->resolver = $resolver;
        $this->namespace = $namespace;
    }

    public function find(string $id, string $class): ProjectionInterface
    {
        $key = $this->resolveKey($id, $class);
        $projection = $this->getMemcached()->get($key);
        if (!$projection) {
            throw ProjectionNotFoundException::forProjection($class, $id);
        }

        return $projection;
    }

    public function findBy(string $class): Generator
    {
        $keys = $this->getMemcached()->get($class);
        if (!$keys) {
            $keys = [];
        }
        foreach ($keys as $key) {
            yield $this->getMemcached()->get($key);
        }
    }

    public function has(string $id, string $class): bool
    {
        $key = $this->resolveKey($id, $class);
        return (bool) $this->getMemcached()->get($key);
    }

    public function commit(UnitOfWork $unit): void
    {
        foreach ($unit->getStored() as $projection) {
            $this->store($projection);
        }
        foreach ($unit->getRemoved() as $descriptor) {
            $this->remove($descriptor->getId(), $descriptor->getClass());
        }
    }

    public function purge(): void
    {
        $keys = $this->getMemcached()->get($this->namespace . ':keys');
        if (!$keys) {
            $keys = [];
        }
        foreach ($keys as $key) {
            $this->getMemcached()->delete($key);
        }

        $classes = $this->getMemcached()->get($this->namespace . ':classes');
        if ($classes) {
            foreach ($classes as $class) {
                $this->getMemcached()->delete($class);
            }
        }
        $this->getMemcached()->set($this->namespace . ':keys', [], 0);
    }

    private function getMemcached(): Memcached
    {
        if (!$this->memcached) {
            $resolver = $this->resolver;
            $memcached = $resolver();
            if (!$memcached instanceof Memcached) {
                throw new RuntimeException('Resolver does not return an instance of Memcached.');
            }
            $this->memcached = $memcached;
        }
        return $this->memcached;
    }

    private function store(ProjectionInterface $projection): void
    {
        $class = get_class($projection);
        $id = $projection->getId();
        $key = $this->resolveKey($id, $class);
        $this->getMemcached()->set($key, $projection, 0);

        $classes = $this->getMemcached()->get($this->namespace . ':classes');
        if (!$classes) {
            $classes = [];
        }
        if (!in_array($class, $classes)) {
            $classes[] = $class;
            $this->getMemcached()->set($this->namespace . ':classes', $classes, 0);
        }

        $keys = $this->getMemcached()->get($this->namespace . ':keys');
        if (!$keys) {
            $keys = [];
        }
        if (!in_array($key, $keys)) {
            $keys[$key] = $key;
            $this->getMemcached()->set($this->namespace . ':keys', $keys, 0);
        }

        $keys = $this->getMemcached()->get($class);
        if (!$keys) {
            $keys = [];
        }
        if (!in_array($key, $keys)) {
            $keys[$key] = $key;
            $this->getMemcached()->set($class, $keys, 0);
        }
    }

    private function remove(string $id, string $class): void
    {
        $key = $this->resolveKey($id, $class);
        $this->getMemcached()->delete($key);

        $keys = $this->getMemcached()->get($class);
        unset($keys[$key]);
        $this->getMemcached()->set($class, $keys, 0);

        $keys = $this->getMemcached()->get($this->namespace . ':keys');
        unset($keys[$key]);
        $this->getMemcached()->set($this->namespace . ':keys', $keys, 0);
    }

    private function resolveKey(string $id, string $class): string
    {
        return sprintf($this->namespace . ':projections:%s:%s', $id, $class);
    }
}
