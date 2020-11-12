<?php

declare(strict_types=1);

namespace BusFactor\Example\Aggregate;

use BusFactor\Aggregate\AggregateFactory;
use BusFactor\AggregateStore\AggregateStore;
use BusFactor\AggregateStore\AggregateStoreInterface;
use BusFactor\EventBus\EventBusInterface;
use BusFactor\EventSourcingAggregateStore\EventSourcingAggregateStoreAdapter;
use BusFactor\EventStore\EventStoreInterface;

class PlayerRepository
{
    private AggregateStoreInterface $store;

    public function __construct(EventStoreInterface $eventStore, EventBusInterface $eventBus)
    {
        $this->store = new AggregateStore(
            new EventSourcingAggregateStoreAdapter(
                new AggregateFactory(Player::class),
                $eventStore,
                $eventBus
            )
        );
    }

    public function find(string $playerId): Player
    {
        /** @var Player $player */
        $player = $this->store->find($playerId, Player::getType());
        return $player;
    }

    public function exists(string $playerId): bool
    {
        return $this->store->has($playerId, Player::getType());
    }

    public function store(Player $player): void
    {
        $this->store->store($player);
    }
}
