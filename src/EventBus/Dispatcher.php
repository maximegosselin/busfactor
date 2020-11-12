<?php

declare(strict_types=1);

namespace BusFactor\EventBus;

use BusFactor\Aggregate\RecordedEvent;
use BusFactor\Aggregate\Stream;

final class Dispatcher implements EventStreamPublisherInterface
{
    /** @var EventHandlerInterface[][] */
    private array $subscribers = [];

    public function publish(Stream $stream): void
    {
        $recordedEvents = $stream->getRecordedEvents();
        foreach ($recordedEvents as $recordedEvent) {
            $this->notifySubscribers($stream->getStreamId(), $recordedEvent);
        }
    }

    public function subscribe(string $eventClass, EventHandlerInterface $subscriber): void
    {
        if (!isset($this->subscribers[$eventClass])) {
            $this->subscribers[$eventClass] = [];
        }
        $this->subscribers[$eventClass][] = $subscriber;
    }

    private function notifySubscribers(string $aggregateId, RecordedEvent $recordedEvent): void
    {
        $subscribers = $this->resolveSubscribers($recordedEvent);
        foreach ($subscribers as $subscriber) {
            $subscriber->handle($aggregateId, $recordedEvent);
        }
    }

    /** @return EventHandlerInterface[] */
    private function resolveSubscribers(RecordedEvent $recordedEvent): array
    {
        $name = get_class($recordedEvent->getEvent());
        if (!isset($this->subscribers[$name])) {
            return [];
        }
        $subscribers = [];
        foreach ($this->subscribers[$name] as $subscriber) {
            $subscribers[] = $subscriber;
        }
        return $subscribers;
    }
}
