<?php

declare(strict_types=1);

namespace BusFactor\ProjectionStore;

use Exception;

class ProjectionNotFoundException extends Exception
{
    public static function forProjection(string $class, string $id): self
    {
        $message = sprintf('Projection with class %s and ID %s not found.', $class, $id);

        return new static($message);
    }
}
