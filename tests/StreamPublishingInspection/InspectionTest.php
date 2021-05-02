<?php

declare(strict_types=1);

namespace BusFactor\Test\StreamPublishingInspection;

use BusFactor\Aggregate\Metadata;
use BusFactor\Aggregate\RecordedEvent;
use BusFactor\Aggregate\Stream;
use BusFactor\EventBus\EventBusInterface;
use BusFactor\EventStore\InMemoryEventStoreAdapter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class InspectionTest extends TestCase
{
    /** @test */
    public function it_publishes_inspected_events(): void
    {
        /** @var EventBusInterface|MockObject $eventBus */
        $eventBus = $this->createMock(EventBusInterface::class);
        $eventBus->expects($this->exactly(3))->method('publish');

        $adapter = new InMemoryEventStoreAdapter();
        $adapter->append(
            (new Stream('123', 'type'))
                ->withRecordedEvent(RecordedEvent::createNow(new TestEvent(), new Metadata(), 1))
                ->withRecordedEvent(RecordedEvent::createNow(new TestEvent(), new Metadata(), 2))
                ->withRecordedEvent(RecordedEvent::createNow(new TestEvent(), new Metadata(), 3))
        );

        (new Inspection($adapter, $eventBus))->start();
    }
}
