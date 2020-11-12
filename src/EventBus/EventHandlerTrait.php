<?php

declare(strict_types=1);

namespace BusFactor\EventBus;

use BusFactor\Aggregate\RecordedEvent;
use RuntimeException;

trait EventHandlerTrait
{
    public function handle(string $aggregateId, RecordedEvent $recordedEvent): void
    {
        $event = $recordedEvent->getEvent();

        $classParts = explode('\\', get_class($event));
        $method = 'handle' . end($classParts);

        if (method_exists($this, $method)) {
            $this->$method($aggregateId, $event, $recordedEvent);
        } else {
            throw new RuntimeException(sprintf(
                'Function "%s" must be implemented in class %s',
                $method,
                get_class($this)
            ));
        }
    }
}
