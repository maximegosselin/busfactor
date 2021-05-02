<?php

declare(strict_types=1);

namespace BusFactor\StreamPublishingInspection;

use BusFactor\EventBus\EventBusInterface;
use BusFactor\EventStore\AdapterInterface;

final class Inspection
{
    private AdapterInterface $adapter;

    private EventBusInterface $eventBus;

    public function __construct(AdapterInterface $adapter, EventBusInterface $eventBus)
    {
        $this->adapter = $adapter;
        $this->eventBus = $eventBus;
    }

    public function start(?callable $beforeEvent = null, ?callable $afterEvent = null): void
    {
        $this->adapter->inspect(new Inspector($this->eventBus, $beforeEvent, $afterEvent));
    }
}
