# BusFactor 2.x Documentation

## Content

- What is BusFactor
- Installation
- Requirements
- Disclaimer
- Building Blocks
    - [The Command Bus](command-bus.md)
    - Aggregate Modeling
    - The Event Bus
    - Projections

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
