<?php

declare(strict_types=1);

namespace BusFactor\PdoEventStore;

use BusFactor\Aggregate\RecordedEvent;
use BusFactor\Aggregate\Stream;
use BusFactor\EventStore\AdapterInterface;
use BusFactor\EventStore\ConcurrencyException;
use BusFactor\EventStore\InspectorInterface;
use BusFactor\EventStore\StreamNotFoundException;
use BusFactor\EventStream\Metadata;
use BusFactor\EventStream\StreamEventInterface;
use BusFactor\Pdo\PdoInterface;
use BusFactor\Uuid\Uuid;
use DateTimeImmutable;
use Exception;
use PDO;
use PDOException;

final class PdoEventStoreAdapter implements AdapterInterface
{
    private PdoInterface $pdo;

    private Config $config;

    public function __construct(PdoInterface $pdo, Config $config)
    {
        $this->pdo = $pdo;
        $this->config = $config;
    }

    public function fetch(string $streamId, string $streamType, int $fromVersion = 0): Stream
    {
        $selectColumns = [
            $this->config->getAlias('stream_type'),
            $this->config->getAlias('stream_id'),
            $this->config->getAlias('stream_version'),
            $this->config->getAlias('event_id'),
            $this->config->getAlias('event_class'),
            $this->config->getAlias('event_metadata'),
            $this->config->getAlias('event_payload'),
            $this->config->getAlias('event_time'),
        ];
        $sql = sprintf(
            'select %s from %s where %s = :streamType and %s = :streamId and %s >= :streamVersion order by %s asc',
            implode(',', $selectColumns),
            $this->config->getTable(),
            $this->config->getAlias('stream_type'),
            $this->config->getAlias('stream_id'),
            $this->config->getAlias('stream_version'),
            $this->config->getAlias('stream_version'),
        );
        $query = $this->pdo->prepare($sql);
        $query->execute(
            [
                ':streamType' => $streamType,
                ':streamId' => $streamId,
                ':streamVersion' => $fromVersion,
            ]
        );
        $rows = $query->fetchAll(PDO::FETCH_ASSOC);
        if (!count($rows) && ($fromVersion === 0)) {
            throw new StreamNotFoundException($streamId);
        }

        $stream = new Stream($streamId, $streamType);
        foreach ($rows as $row) {
            $stream = $stream->withRecordedEvent($this->buildEventFromRow($row));
        }

        return $stream;
    }

    public function streamExists(string $streamId, string $streamType): bool
    {
        $sql = sprintf(
            'select count(*) event_count from %s where %s = :streamType and %s = :streamId',
            $this->config->getTable(),
            $this->config->getAlias('stream_type'),
            $this->config->getAlias('stream_id'),
        );
        $query = $this->pdo->prepare($sql);
        $query->execute(
            [
                ':streamType' => $streamType,
                ':streamId' => $streamId,
            ]
        );
        $count = $query->fetchAll(PDO::FETCH_ASSOC)[0]['event_count'];
        return (int) $count > 0;
    }

    public function append(Stream $stream, ?int $expectedVersion = null): void
    {
        if ($expectedVersion > 0) {
            $version = $this->getVersion($stream->getStreamId(), $stream->getStreamType());
            if ($version != $expectedVersion) {
                $message = sprintf(
                    'Version for stream %s-%s is %s, expected %s.',
                    $stream->getStreamType(),
                    $stream->getStreamId(),
                    $version,
                    $expectedVersion
                );
                throw new ConcurrencyException($message);
            }
        }

        $insertColumns = [
            $this->config->getAlias('stream_type'),
            $this->config->getAlias('stream_id'),
            $this->config->getAlias('stream_version'),
            $this->config->getAlias('event_id'),
            $this->config->getAlias('event_class'),
            $this->config->getAlias('event_metadata'),
            $this->config->getAlias('event_payload'),
            $this->config->getAlias('event_time'),
        ];
        $sql = sprintf(
            'insert into %s (%s) values (:streamType, :streamId, :version, :id, :class, :metadata, :payload, :time)',
            $this->config->getTable(),
            implode(',', $insertColumns)
        );
        $query = $this->pdo->prepare($sql);
        foreach ($stream->getRecordedEvents() as $recordedEvent) {
            /** @var RecordedEvent $recordedEvent */
            $values = $this->buildValuesFromEvent($recordedEvent);
            $metadata = json_encode($values['metadata']);
            $payload = json_encode($values['payload']);
            try {
                $query->execute(
                    [
                        ':streamType' => $stream->getStreamType(),
                        ':streamId' => $stream->getStreamId(),
                        ':version' => $values['version'],
                        ':id' => Uuid::new(),
                        ':class' => $values['class'],
                        ':metadata' => $metadata === '[]' ? '{}' : $metadata,
                        ':payload' => $payload === '[]' ? '{}' : $payload,
                        ':time' => $values['time'],
                    ]
                );
                if (json_last_error()) {
                    throw new JsonSerializationException(json_last_error_msg());
                }
            } catch (Exception $e) {
                if ($e instanceof PDOException) {
                    throw new ConcurrencyException(
                        sprintf(
                            'Version %s for stream %s-%s already exists.',
                            $values['version'],
                            $stream->getStreamType(),
                            $stream->getStreamId()
                        )
                    );
                }
            }
        }
    }

    public function getVersion(string $streamId, string $streamType): int
    {
        $sql = sprintf(
            'select max(%s) max_stream_version from %s where %s = :streamType and %s = :streamId',
            $this->config->getAlias('stream_version'),
            $this->config->getTable(),
            $this->config->getAlias('stream_type'),
            $this->config->getAlias('stream_id'),
        );
        $query = $this->pdo->prepare($sql);
        $query->execute(
            [
                ':streamType' => $streamType,
                ':streamId' => $streamId,
            ]
        );
        $version = $query->fetchAll(PDO::FETCH_ASSOC)[0]['max_stream_version'];
        if (!$version) {
            throw new StreamNotFoundException($streamId);
        }

        return (int) $version;
    }

    public function inspect(InspectorInterface $inspector): void
    {
        $reverse = $inspector->getFilter()->isReverse();
        $max = $inspector->getFilter()->getLimit();
        $filteredEvents = $inspector->getFilter()->getClasses();

        if (count($filteredEvents)) {
            $questionMarks = str_repeat('?,', count($filteredEvents) - 1) . '?';
            $whereClause = sprintf(
                'where %s in (%s)',
                $this->config->getAlias('event_class'),
                $questionMarks
            );
        } else {
            $whereClause = '';
        }

        $limitClause = $max ? 'limit ' . $max : '';
        $sql = sprintf(
            'select * from %s %s order by %s %s %s',
            $this->config->getTable(),
            $whereClause,
            $this->config->getAlias('sequence'),
            $reverse ? 'desc' : 'asc',
            $limitClause
        );
        $query = $this->pdo->prepare($sql);
        $query->execute($filteredEvents);
        $events = [];
        while ($row = $query->fetch()) {
            $event = [
                'stream_id' => $row[$this->config->getAlias('stream_id')],
                'stream_type' => $row[$this->config->getAlias('stream_type')],
                'event' => $this->buildEventFromRow($row),
            ];
            if ($this->config->getEventBuffering()) {
                $events[] = $event;
            } else {
                $inspector->inspect($event['stream_id'], $event['stream_type'], $event['event']);
            }
        };
        if ($this->config->getEventBuffering()) {
            while ($event = array_shift($events)) {
                $inspector->inspect($event['stream_id'], $event['stream_type'], $event['event']);
            }
        }
    }

    public function purge(): void
    {
        $sql = sprintf('delete from %s where 1=1', $this->config->getTable());
        $this->pdo->exec($sql);
    }

    private function buildEventFromRow(array $row): RecordedEvent
    {
        $version = (int) $row[$this->config->getAlias('stream_version')];
        $metadata = new Metadata(
            json_decode(
                $row[$this->config->getAlias('event_metadata')],
                true
            )
        );
        $payload = json_decode($row[$this->config->getAlias('event_payload')], true);
        $class = $row[$this->config->getAlias('event_class')];
        $classRevision = (int) $metadata->get('revision');
        /** @var StreamEventInterface $event */
        $event = $class::deserialize($payload);
        $recordTime = new DateTimeImmutable($row[$this->config->getAlias('event_time')]);

        if ($event::getRevision() != $classRevision) {
            throw new RevisionMismatchException(
                sprintf(
                    'Class revision mismatch for class %s. Got %s, expected %s.',
                    $class,
                    $classRevision,
                    $event::getRevision()
                )
            );
        }

        if (json_last_error()) {
            throw new JsonSerializationException(json_last_error_msg());
        }

        return RecordedEvent::create($event, $metadata, $version, $recordTime);
    }

    private function buildValuesFromEvent(RecordedEvent $recordedEvent): array
    {
        $values = [
            'version' => $recordedEvent->getVersion(),
            'metadata' => $recordedEvent->getMetadata()
                ->with('revision', $recordedEvent->getEvent()::getRevision())
                ->toArray(),
            'class' => get_class($recordedEvent->getEvent()),
            'payload' => $recordedEvent->getEvent()->serialize(),
            'time' => $recordedEvent->getRecordTime()->format('Y-m-d\TH:i:s.uP'),
        ];

        if (json_last_error()) {
            throw new JsonSerializationException(json_last_error_msg());
        }

        return $values;
    }
}
