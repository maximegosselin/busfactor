<?php

declare(strict_types=1);

namespace BusFactor\Aggregate;

class TestAggregate implements AggregateInterface
{
    use AggregateRootTrait;

    public static function getType(): string
    {
        return 'test';
    }

    public static function create(string $id): self
    {
        return new static($id);
    }

    public function action(): void
    {
        $this->apply(new TestEvent());
    }
}
