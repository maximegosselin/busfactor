<?php

declare(strict_types=1);

namespace BusFactor\Testing;

use BusFactor\Testing\Constraint\ProjectionsMustContainExactly;
use BusFactor\Testing\Constraint\ProjectionsMustContainOnly;
use BusFactor\Testing\Constraint\ProjectionsMustCount;
use BusFactor\Testing\Constraint\ProjectionsMustNotContain;
use BusFactor\Testing\Constraint\StreamMustExist;
use BusFactor\Testing\Constraint\StreamsMustContain;
use BusFactor\Testing\Constraint\StreamsMustContainExactly;
use BusFactor\Testing\Constraint\StreamsMustCount;

trait AssertionsTrait
{
    public static function assertPublishedStreamWithId(string $streamId, PublishedStreams $publishedStreams): void
    {
        self::assertThat($publishedStreams, new StreamMustExist($streamId));
    }

    public static function assertPublishedStreamsDoNotContain(
        string $eventClass,
        PublishedStreams $publishedStreams
    ): void {
        self::assertPublishedStreamsContainExactly([$eventClass => 0], $publishedStreams);
    }

    public static function assertPublishedStreamsContainExactly(
        array $eventsAndCount,
        PublishedStreams $publishedStreams
    ): void {
        foreach ($eventsAndCount as $event => $count) {
            self::assertThat($publishedStreams, new StreamsMustContainExactly($count, $event));
        }
    }

    public static function assertPublishedStreamsContains(string $eventClass, PublishedStreams $publishedStreams): void
    {
        foreach ((array) $eventClass as $event) {
            self::assertThat($publishedStreams, new StreamsMustContain($event));
        }
    }

    public static function assertPublishedStreamsCount(int $count, PublishedStreams $publishedStreams): void
    {
        self::assertThat($publishedStreams, new StreamsMustCount($count));
    }

    public static function assertUpdatedProjectionsContainExactly(
        array $projectionsAndCount,
        UpdatedProjections $updatedProjections
    ): void {
        foreach ($projectionsAndCount as $projection => $count) {
            self::assertThat($updatedProjections, new ProjectionsMustContainExactly($count, $projection));
        }
    }

    public static function assertUpdatedProjectionsDoNotContain(
        string $projectionClass,
        UpdatedProjections $updatedProjections
    ): void {
        self::assertThat($updatedProjections, new ProjectionsMustNotContain($projectionClass));
    }

    public static function assertUpdatedProjectionsContainOnly(
        string $projectionClass,
        UpdatedProjections $updatedProjections
    ): void {
        self::assertThat($updatedProjections, new ProjectionsMustContainOnly($projectionClass));
    }

    public static function assertUpdatedProjectionsCount(int $count, UpdatedProjections $updatedProjections): void
    {
        self::assertThat($updatedProjections, new ProjectionsMustCount($count));
    }
}
