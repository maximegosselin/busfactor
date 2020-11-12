<?php

declare(strict_types=1);

namespace BusFactor\Scenario\Constraint;

use BusFactor\Scenario\PublishedStreams;
use PHPUnit\Framework\Constraint\Constraint;
use ReflectionClass;

final class StreamsMustContainExactly extends Constraint
{
    private int $count;

    private string $eventClass;

    private int $found;

    public function __construct(int $count, string $eventClass)
    {
        if ((new ReflectionClass(Constraint::class))->hasMethod('__construct')) {
            parent::__construct();
        }
        $this->count = $count;
        $this->eventClass = $eventClass;
        $this->found = 0;
    }

    public function toString(): string
    {
        return " contain exactly {$this->count} instance(s) of {$this->eventClass}, found {$this->found}";
    }

    /**
     * @param PublishedStreams $publishedStreams
     */
    public function matches($publishedStreams): bool
    {
        $this->found = 0;
        foreach ($publishedStreams->getAll() as $stream) {
            foreach ($stream->getRecordedEvents() as $recordedEvent) {
                if (get_class($recordedEvent->getEvent()) === $this->eventClass) {
                    $this->found++;
                }
            }
        }
        return $this->found === $this->count;
    }

    protected function failureDescription($other): string
    {
        return 'published streams' . $this->toString();
    }
}
