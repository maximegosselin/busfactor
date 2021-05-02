<?php

declare(strict_types=1);

namespace BusFactor\Test\EventBus;

use BusFactor\Aggregate\RecordedEvent;
use BusFactor\Aggregate\StreamEventInterface;

class TestEventHandler1 implements EventHandlerInterface
{
    use EventHandlerTrait;

    /** @var StreamEventInterface[] */
    private array $handledEvents = [];

    public static function getSubscribedEventClasses(): array
    {
        return [
            TestEvent1::class,
        ];
    }

    /** @return StreamEventInterface[] */
    public function getHandledEvents(): array
    {
        return $this->handledEvents;
    }

    private function handleTestEvent1(string $aggregateId, TestEvent1 $event, RecordedEvent $envelope): void
    {
        $this->handledEvents[] = $event;
    }
}
