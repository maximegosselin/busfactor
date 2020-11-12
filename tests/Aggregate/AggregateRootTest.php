<?php

declare(strict_types=1);

namespace BusFactor\Aggregate;

use PHPUnit\Framework\TestCase;

class AggregateRootTest extends TestCase
{
    /** @test */
    public function it_creates_new_aggregate(): void
    {
        $aggregate = TestAggregate::create('123');

        $this->assertSame('123', $aggregate->getAggregateId());
    }

    /** @test */
    public function it_records_events(): void
    {
        $aggregate = TestAggregate::create('123');

        $aggregate->action();
        $aggregate->action();
        $aggregate->action();
        $this->assertCount(3, $aggregate->pullNewEvents());
        $this->assertCount(0, $aggregate->peekNewEvents());
    }
}
