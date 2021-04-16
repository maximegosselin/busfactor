<?php

declare(strict_types=1);

namespace BusFactor\Extra\PdoProjectionStore;

use BusFactor\ProjectionStore\ProjectionStore;
use BusFactor\Serialization\ObjectSerializer;
use BusFactor\Serialization\SerializeFunctionObjectSerializer;
use BusFactor\Util\PdoProxy;
use PDO;
use PHPUnit\Framework\TestCase;

class PdoProjectionStoreAdapterTest extends TestCase
{
    /** @test */
    public function it_persists_with_pdo(): void
    {
        $pdo = new PdoProxy(function (): PDO {
            $pdo = new PDO('sqlite::memory:');
            $pdo->exec('CREATE TABLE projection_store (
                projection_id VARCHAR,
                projection_class VARCHAR,
                projection_payload BLOB    
            )');
            return $pdo;
        });
        $serializer = new ObjectSerializer(new SerializeFunctionObjectSerializer());

        $store = new ProjectionStore(new PdoProjectionStoreAdapter($pdo, $serializer, new Config()));
        $store->store(new TestProjection('123'));
        $store->commit();

        $projection = $store->find('123', TestProjection::class);

        $this->assertEquals(new TestProjection('123'), $projection);
    }
}
