<?php

declare(strict_types=1);

namespace BusFactor\MongoProjectionStoreAdapter;

use BusFactor\Projection\ProjectionInterface;
use BusFactor\ProjectionStore\AdapterInterface;
use BusFactor\ProjectionStore\ProjectionNotFoundException;
use BusFactor\ProjectionStore\UnitOfWork;
use Generator;
use MongoDB\Collection;
use MongoDB\Driver\BulkWrite;
use RuntimeException;

class MongoProjectionStoreAdapter implements AdapterInterface
{
    /** @var callable */
    private $resolver;

    private ?Collection $collection = null;

    public function __construct(callable $resolver)
    {
        $this->resolver = $resolver;
    }

    /** @throws ProjectionNotFoundException */
    public function find(string $id, string $class): ProjectionInterface
    {
        $doc = $this->getCollection()->findOne([
            '_id' => self::resolveKey($id, $class),
        ]);
        if (!$doc) {
            throw new ProjectionNotFoundException();
        }
        return unserialize($doc['projection']);
    }

    public function findBy(string $class): Generator
    {
        $docs = $this->getCollection()->find([
            'class' => $class,
        ]);
        foreach ($docs as $doc) {
            yield unserialize($doc['projection']);
        }
    }

    public function has(string $id, string $class): bool
    {
        return !is_null($this->getCollection()->findOne([
            '_id' => self::resolveKey($id, $class),
        ]));
    }

    public function commit(UnitOfWork $unit): void
    {
        $bulk = new BulkWrite(['ordered' => false]);
        foreach ($unit->getStored() as $projection) {
            $class = get_class($projection);
            $bulk->update([
                '_id' => self::resolveKey($projection->getId(), $class),
            ], [
                '$set' => [
                    'class' => $class,
                    'projection' => serialize($projection),
                ],
            ], [
                'upsert' => true,
            ]);
        }
        foreach ($unit->getRemoved() as $projectionDescriptor) {
            $bulk->delete([
                '_id' => self::resolveKey($projectionDescriptor->getId(), $projectionDescriptor->getClass()),
            ]);
        }
        if ($bulk->count()) {
            $this->getCollection()->getManager()->executeBulkWrite($this->getCollection()->getNamespace(), $bulk);
        }
    }

    public function purge(): void
    {
        $this->getCollection()->drop();
    }

    private function getCollection(): Collection
    {
        if (!$this->collection) {
            $resolver = $this->resolver;
            $collection = $resolver();
            if (!$collection instanceof Collection) {
                throw new RuntimeException('Resolver does not return an instance of MongoDB\Collection.');
            }
            $this->collection = $collection;
        }
        return $this->collection;
    }

    private static function resolveKey(string $id, string $class): string
    {
        return sprintf('%s-%s', $id, $class);
    }
}
