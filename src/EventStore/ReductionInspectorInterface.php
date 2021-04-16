<?php

declare(strict_types=1);

namespace BusFactor\EventStore;

interface ReductionInspectorInterface extends InspectorInterface
{
    /** @return mixed */
    public function getResult();

    public function reset(): void;
}
