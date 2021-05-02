<?php

declare(strict_types=1);

namespace BusFactor\Test\EventStore;

use BusFactor\Aggregate\Metadata;
use BusFactor\Aggregate\RecordedEvent;
use BusFactor\Aggregate\Stream;
use BusFactor\EventStoreReductionInspection\EventStoreReductionInspection;
use PHPUnit\Framework\TestCase;

class ReductionTest extends TestCase
{
    /** @test */
    public function it_reduces_events(): void
    {
        $eventStore = new EventStore(new InMemoryEventStoreAdapter());
        $eventStore->append(
            (new Stream('123', 'type'))
                ->withRecordedEvent(RecordedEvent::createNow(new TestEvent(), new Metadata(), 1))
                ->withRecordedEvent(RecordedEvent::createNow(new TestEvent(), new Metadata(), 2))
                ->withRecordedEvent(RecordedEvent::createNow(new TestEvent(), new Metadata(), 3))
        );
        $reduction = new EventStoreReductionInspection($eventStore->getAdapter());

        $this->assertEquals(3, $reduction->inspect(new TestReducer()));
    }
}
