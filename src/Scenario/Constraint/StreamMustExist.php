<?php

declare(strict_types=1);

namespace BusFactor\Scenario\Constraint;

use BusFactor\Scenario\PublishedStreams;
use PHPUnit\Framework\Constraint\Constraint;
use ReflectionClass;

final class StreamMustExist extends Constraint
{
    private string $streamId;

    public function __construct(string $streamId)
    {
        if ((new ReflectionClass(Constraint::class))->hasMethod('__construct')) {
            parent::__construct();
        }
        $this->streamId = $streamId;
    }

    /**
     * @param PublishedStreams $publishedStreams
     */
    public function matches($publishedStreams): bool
    {
        foreach ($publishedStreams->getAll() as $stream) {
            if ($stream->getStreamId() === $this->streamId) {
                return true;
            }
        }
        return false;
    }

    public function toString(): string
    {
        return " contain a stream with ID {$this->streamId}";
    }

    protected function failureDescription($other): string
    {
        return 'published streams' . $this->toString();
    }
}
