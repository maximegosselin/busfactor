<?php

declare(strict_types=1);

namespace BusFactor\Aggregate;

interface AggregateInterface
{
    public static function getType(): string;

    public function getAggregateId(): string;

    public function getVersion(): int;

    public function peekNewEvents(): Stream;

    public function pullNewEvents(): Stream;

    public function replayStream(Stream $stream): void;
}
