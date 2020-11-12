<?php

declare(strict_types=1);

namespace BusFactor\StreamEnricherEventBusMiddleware;

use BusFactor\Aggregate\Metadata;
use BusFactor\Aggregate\RecordedEvent;
use BusFactor\Aggregate\Stream;
use BusFactor\EventBus\EventBus;
use BusFactor\EventBus\EventStreamPublisherInterface;
use BusFactor\EventBus\MiddlewareInterface;
use PHPUnit\Framework\TestCase;

class StreamEnricherEventBusMiddlewareTest extends TestCase
{
    /** @test */
    public function it_enriches_stream(): void
    {
        $mw = new class implements MiddlewareInterface {
            public ?Metadata $metadata = null;

            public function publish(Stream $stream, EventStreamPublisherInterface $next): void
            {
                $this->metadata = $stream->getRecordedEvents()[0]->getMetadata();
                $next->publish($stream);
            }
        };

        $bus = new EventBus();
        $bus->addMiddleware($mw);
        $bus->addMiddleware(new StreamEnricherEventBusMiddleware(new TestEnricher()));

        $stream = (new Stream('123', 'type'))
            ->withRecordedEvent(RecordedEvent::createNow(new TestEvent(), new Metadata(), 1));

        $bus->publish($stream);

        $this->assertEquals($mw->metadata->toArray(), ['foo' => 'bar']);
    }
}
