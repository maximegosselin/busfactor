<?php

declare(strict_types=1);

namespace BusFactor\EventSourcingAggregateStore;

use BusFactor\EventSourcedAggregate\EventSourcedAggregateInterface;
use BusFactor\EventSourcedAggregate\EventSourcedAggregateRootTrait;

class TestAggregate implements EventSourcedAggregateInterface
{
    use EventSourcedAggregateRootTrait;

    public static function getType(): string
    {
        return 'test-aggregate';
    }

    public static function create(string $id): self
    {
        $me = new static($id);
        $me->touch();
        return $me;
    }

    public function touch(): void
    {
        $this->apply(new TestEvent());
    }
}
