<?php

declare(strict_types=1);

namespace BusFactor\ProjectionStoreTransactionCommandBusMiddleware;

use BusFactor\CommandBus\CommandDispatcherInterface;
use BusFactor\CommandBus\MiddlewareInterface;
use BusFactor\ProjectionStore\ProjectionStoreInterface;
use Throwable;

final class ProjectionStoreTransactionCommandBusMiddleware implements MiddlewareInterface
{
    private ProjectionStoreInterface $projections;

    private int $nestedLevels;

    public function __construct(ProjectionStoreInterface $projections)
    {
        $this->projections = $projections;
        $this->nestedLevels = 0;
    }

    public function dispatch(object $command, CommandDispatcherInterface $next): void
    {
        try {
            $this->nestedLevels++;
            $next->dispatch($command);
            $this->nestedLevels--;
            if ($this->nestedLevels === 0) {
                $this->projections->commit();
            }
        } catch (Throwable $t) {
            $this->projections->rollback();
            throw $t;
        }
    }
}
