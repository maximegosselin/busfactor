<?php

declare(strict_types=1);

namespace BusFactor\StreamEnricherEventStoreMiddleware;

use BusFactor\Aggregate\Metadata;
use BusFactor\Aggregate\RecordedEvent;
use BusFactor\Aggregate\Stream;
use BusFactor\EventStore\EventStore;
use BusFactor\EventStore\EventStoreInterface;
use BusFactor\EventStore\InMemoryEventStoreAdapter;
use BusFactor\EventStore\InspectorInterface;
use BusFactor\EventStore\MiddlewareInterface;
use BusFactor\Support\StreamEnricher\StreamEnricherEventStoreMiddleware;
use PHPUnit\Framework\TestCase;

class StreamEnricherEventStoreMiddlewareTest extends TestCase
{
    /** @test */
    public function it_enriches_stream(): void
    {
        $mw = new class implements MiddlewareInterface {
            public ?Metadata $metadata = null;

            public function fetch(string $streamId, string $streamType, int $fromVersion, EventStoreInterface $next): Stream
            {
                return $next->fetch($streamId, $streamType, $fromVersion);
            }

            public function streamExists(string $streamId, string $streamType, EventStoreInterface $next): bool
            {
                return $next->streamExists($streamId, $streamType);
            }

            public function getVersion(string $streamId, string $streamType, EventStoreInterface $next): int
            {
                return $next->getVersion($streamId, $streamType);
            }

            public function append(Stream $stream, ?int $expectedVersion, EventStoreInterface $next): void
            {
                $this->metadata = $stream->getRecordedEvents()[0]->getMetadata();
                $next->append($stream, $expectedVersion);
            }

            public function inspect(InspectorInterface $inspector, EventStoreInterface $next): void
            {
                $next->inspect($inspector);
            }

            public function purge(EventStoreInterface $next): void
            {
                $next->purge();
            }
        };

        $store = new EventStore(new InMemoryEventStoreAdapter());
        $store->addMiddleware($mw);
        $store->addMiddleware(new StreamEnricherEventStoreMiddleware(new TestEnricher()));

        $stream = (new Stream('123', 'type'))
            ->withRecordedEvent(RecordedEvent::createNow(new TestEvent(), new Metadata(), 1));

        $store->append($stream);

        $this->assertEquals($mw->metadata->toArray(), ['foo' => 'bar']);
    }
}
