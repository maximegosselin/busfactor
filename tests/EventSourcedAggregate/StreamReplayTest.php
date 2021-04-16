<?php

declare(strict_types=1);

namespace BusFactor\EventSourcedAggregate;

use BusFactor\Aggregate\RecordedEvent;
use BusFactor\Aggregate\Stream;
use PHPStan\Testing\TestCase;

class StreamReplayTest extends TestCase
{
    /** @test */
    public function it_replays_stream(): void
    {
        $agg1 = TestEventSourcedAggregate::create('123', 'john');
        $agg1->setName('Bill');

        $stream = array_reduce($agg1->pullNewEvents(), function (Stream $stream, RecordedEvent $event) {
            return $stream->withRecordedEvent(RecordedEvent::fromRecordedEvent($event));
        }, new Stream($agg1->getAggregateId(), $agg1::getType()));

        $agg2 = new TestEventSourcedAggregate('123');
        $agg2->replayStream($stream);

        $this->assertEquals($agg1->getName(), $agg2->getName());
    }
}
