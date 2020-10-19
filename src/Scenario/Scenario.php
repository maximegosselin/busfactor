<?php

declare(strict_types=1);

namespace BusFactor\Scenario;

use BusFactor\CommandBus\CommandBus;
use BusFactor\CommandBus\CommandBusInterface;
use BusFactor\EventBus\EventBus;
use BusFactor\EventStore\EventStore;
use BusFactor\EventStore\EventStoreInterface;
use BusFactor\EventStore\InMemoryEventStoreAdapter;
use BusFactor\ProjectionStore\InMemoryProjectionStoreAdapter;
use BusFactor\ProjectionStore\ProjectionStore;

class Scenario
{
    private EventBus $eventBus;

    private CommandBusInterface $commandBus;

    private EventStoreInterface $eventStore;

    private EventBusTraceMiddleware $eventBusTrace;

    private ProjectionStoreTraceMiddleware $projectionStoreTrace;

    public function __construct(
        ?EventBus $eventBus = null,
        ?CommandBusInterface $commandBus = null,
        ?ProjectionStore $projectionStore = null,
        ?EventStoreInterface $eventStore = null
    ) {
        $this->eventBus = $eventBus ?? new EventBus();
        $this->commandBus = $commandBus ?? new CommandBus();
        $this->eventStore = $eventStore ?? new EventStore(new InMemoryEventStoreAdapter());
        $this->eventBusTrace = new EventBusTraceMiddleware();
        $this->eventBus->addMiddleware($this->eventBusTrace);
        $this->projectionStoreTrace = new ProjectionStoreTraceMiddleware();
        ($projectionStore ?? new ProjectionStore(new InMemoryProjectionStoreAdapter()))->addMiddleware($this->projectionStoreTrace);
    }

    public function play(Play ...$plays): void
    {
        foreach ($plays as $play) {
            $play->run(
                $this->eventBus,
                $this->eventBusTrace,
                $this->eventStore,
                $this->commandBus,
                $this->projectionStoreTrace
            );
        }
    }
}
