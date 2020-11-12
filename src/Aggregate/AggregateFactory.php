<?php

declare(strict_types=1);

namespace BusFactor\Aggregate;

use InvalidArgumentException;

final class AggregateFactory
{
    private string $aggregateRootClass;

    public function __construct(string $aggregateRootClass)
    {
        if (!in_array(AggregateInterface::class, class_implements($aggregateRootClass))) {
            $message = 'Class ' . $aggregateRootClass . ' must implement ' . AggregateInterface::class;
            throw new InvalidArgumentException($message);
        }
        $this->aggregateRootClass = $aggregateRootClass;
    }

    public function getAggregateRootClass(): string
    {
        return $this->aggregateRootClass;
    }

    public function rebuildFromStream(Stream $stream): AggregateInterface
    {
        $class = $this->aggregateRootClass;
        /** @var AggregateInterface $aggregate */
        $aggregate = new $class($stream->getStreamId());
        $aggregate->replayStream($stream);
        return $aggregate;
    }
}
