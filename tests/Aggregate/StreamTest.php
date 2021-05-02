<?php

declare(strict_types=1);

namespace BusFactor\Test\Aggregate;

use BusFactor\Aggregate\Metadata;
use BusFactor\Aggregate\RecordedEvent;
use BusFactor\Aggregate\Stream;
use PHPUnit\Framework\TestCase;

class StreamTest extends TestCase
{
    /** @test */
    public function it_returns_lowest_and_highest_version(): void
    {
        $stream = (new Stream('1', 'foo'))
            ->withRecordedEvent(RecordedEvent::createNow(new TestEvent('abc', 123, []), new Metadata(), 1))
            ->withRecordedEvent(RecordedEvent::createNow(new TestEvent('abc', 123, []), new Metadata(), 2))
            ->withRecordedEvent(RecordedEvent::createNow(new TestEvent('abc', 123, []), new Metadata(), 3));

        $this->assertEquals(1, $stream->getLowestVersion());
        $this->assertEquals(3, $stream->getHighestVersion());
    }
}
