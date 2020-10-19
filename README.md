# BusFactor

[![Latest Version](https://img.shields.io/github/release/busfactor/busfactor.svg)](https://github.com/busfactor/busfactor/releases)
[![Composer](https://img.shields.io/badge/composer-busfactor/busfactor-lightgray)](https://packagist.org/packages/busfactor/busfactor)
![PHP](https://img.shields.io/packagist/php-v/busfactor/busfactor)
[![Build Status](https://img.shields.io/travis/busfactor/busfactor.svg)](https://travis-ci.org/busfactor/busfactor)
[![Software License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

BusFactor is a modern PHP library providing several components you can mix and match to implement the CQRS and Event Sourcing patterns in your application.

**Domain-centric** — Implement rich domain logic with aggregates and events.

**Event-driven** — Generate optimized query models and trigger actions in response to domain events.

**Command-oriented** — Decouple application logic from UI by dispatching commands to handlers.

**Test-friendly** — Write scenarios to test your application and domain behavior.

**Framework-agnostic** — Build on any framework or none at all.

**Control-focused** — Plug-in custom middlewares and storage adapters to fit your application's needs.


## Install

Using [Composer](https://getcomposer.org/):

```
$ composer require busfactor/busfactor
```

## Requirements

- PHP >=7.4 with `json` and `pdo` extensions enabled.

## Components

| Component | Description |
| --- | --- |
| `AggregateStore` | Persistence for aggregates. | 
| `Aggregate` | Interfaces and traits for plain DDD aggregates and domain events. | 
| `CacheProjectionStoreMiddleware` | Caching middleware for `ProjectionStore`. | 
| `CommandBus` | Implementation of the Command Bus pattern. | 
| `EventBus` | Implementation of the Publish-Subscribe pattern for event streams. | 
| `EventSourcedAggregateStore` | `AggregateStore` adapter for event-sourced aggregates persistence. | 
| `EventSourcedAggregate` | Interface and trait for event-sourced aggregates. | 
| `EventStoreReductionInspection` | Output single value from `EventStore` inspection. | 
| `EventStore` | Persistence for event streams. | 
| `EventStream` | Event streams for event-sourced aggregates. | 
| `MemcachedProjectionStore` | Memcached adapter for `ProjectionStore`. | 
| `MongoProjectionStoreAdapter` | MongoDB adapter for `ProjectionStore`. | 
| `ObjectSerializer` | Interface for object serialization. | 
| `PdoAggregateStore` | PDO adapter for `AggregateStore`. | 
| `PdoEventStore` | PDO adapter for `EventStore`. | 
| `PdoProjectionStore` | PDO adapter for `ProjectionStore`. | 
| `PdoProxy` | Lazy-connecting PDO proxy. | 
| `PdoTransactionCommandBusMiddleware` | Wrap command dispatch process in a PDO transaction. | 
| `Pdo` | Decorating interface for PHP Data Objects (PDO). | 
| `ProjectionStoreTransactionCommandBusMiddleware` | `ProjectionStore` automatic commit. | 
| `ProjectionStore` | Persistence for projections. | 
| `Projection` | Interface for projections. | 
| `ReflectionObjectSerializer` | Reflection-based adapter for `ObjectSerializer`. | 
| `Scenario` | Testing infrastructure on top of PHPUnit. | 
| `SnapshotAggregateStoreMiddleware` | `AggregateStore` middleware for event-sourced aggregate snapshots. | 
| `StreamEnricherEventBusMiddleware` | `EventBus` middleware for event stream enrichment with `StreamEnricher`. | 
| `StreamEnricherEventStoreMiddleware` | `EventStore` middleware for event stream enrichment with StreamEnricher`. | 
| `StreamEnricher` | Interface for event stream enrichers. | 
| `StreamPublishingInspection` | Publish event streams from `EventStore` inspection. | 
| `Uuid` | Universally Unique IDentifier (UUID) generation. | 

## Testing

```bash
$ vendor/bin/phpunit
```

## Credits

- [Maxime Gosselin](https://github.com/maximegosselin)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
