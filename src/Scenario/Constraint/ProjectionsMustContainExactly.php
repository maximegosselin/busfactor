<?php

declare(strict_types=1);

namespace BusFactor\Scenario\Constraint;

use BusFactor\Scenario\UpdatedProjections;
use PHPUnit\Framework\Constraint\Constraint;
use ReflectionClass;

class ProjectionsMustContainExactly extends Constraint
{
    private int $count;

    private string $projectionClass;

    private int $found;

    public function __construct(int $count, string $projectionClass)
    {
        if ((new ReflectionClass(Constraint::class))->hasMethod('__construct')) {
            parent::__construct();
        }
        $this->count = $count;
        $this->projectionClass = $projectionClass;
        $this->found = 0;
    }

    public function toString(): string
    {
        return "must contain exactly {$this->count} instance(s) of {$this->projectionClass}, found {$this->found}";
    }

    /**
     * @param  UpdatedProjections $updatedProjections
     * @return bool
     */
    public function matches($updatedProjections): bool
    {
        $this->found = 0;
        foreach ($updatedProjections->getAll() as $projection) {
            if (get_class($projection) === $this->projectionClass) {
                $this->found++;
            }
        }
        return $this->found === $this->count;
    }
}
