<?php

declare(strict_types=1);

namespace BusFactor\EventBus;

use BusFactor\Aggregate\RecordedEvent;

interface EventHandlerInterface
{
    public function handle(string $aggregateId, RecordedEvent $recordedEvent): void;
}
