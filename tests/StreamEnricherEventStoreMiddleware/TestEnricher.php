<?php

declare(strict_types=1);

namespace BusFactor\Test\StreamEnricherEventStoreMiddleware;

use BusFactor\Aggregate\RecordedEvent;
use BusFactor\Aggregate\Stream;
use BusFactor\StreamEnricher\StreamEnricherInterface;

class TestEnricher implements StreamEnricherInterface
{
    public function enrich(Stream $stream): Stream
    {
        return array_reduce(
            $stream->getRecordedEvents(),
            function (Stream $enrichedStream, RecordedEvent $envelope) {
                $metadata = $envelope->getMetadata()
                    ->with('foo', 'bar');
                return $enrichedStream->withRecordedEvent($envelope->withMetadata($metadata));
            },
            new Stream($stream->getStreamId(), $stream->getStreamType())
        );
    }
}
