<?php

declare(strict_types=1);

namespace BusFactor\AggregateStore;

use Exception;

class AggregateNotFoundException extends Exception
{
    public static function forAggregate(string $aggregateId, string $aggregateType, ?Exception $previous = null): self
    {
        $message = sprintf('Aggregate of type [%s] with ID [%s] not found.', $aggregateType, $aggregateId);

        return new static($message, 0, $previous);
    }
}
