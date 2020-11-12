<?php

declare(strict_types=1);

namespace BusFactor\Example\Projection;

use BusFactor\Aggregate\RecordedEvent;
use BusFactor\EventBus\EventHandlerInterface;
use BusFactor\EventBus\EventHandlerTrait;
use BusFactor\Example\Aggregate\PlayerNameChangedEvent;
use BusFactor\Example\Aggregate\PlayerRegisteredEvent;
use BusFactor\ProjectionStore\ProjectionNotFoundException;
use BusFactor\ProjectionStore\ProjectionStoreInterface;

class PlayerListProjector implements EventHandlerInterface
{
    use EventHandlerTrait;

    private ProjectionStoreInterface $projections;

    public function __construct(ProjectionStoreInterface $projections)
    {
        $this->projections = $projections;
    }

    public static function getSubscribedEventClasses(): array
    {
        return [
            PlayerRegisteredEvent::class,
            PlayerNameChangedEvent::class,
        ];
    }

    private function handlePlayerRegisteredEvent(string $playerId, PlayerRegisteredEvent $event, RecordedEvent $envelope): void
    {
        $this->projections->store(
            $this->getProjection()->withPlayer($playerId, $event->getNumber(), $event->getName())
        );
    }

    private function handlePlayerNameChangedEvent(string $playerId, PlayerNameChangedEvent $event, RecordedEvent $envelope): void
    {
        $this->projections->store(
            $this->getProjection()->withPlayerName($playerId, $event->getNew())
        );
    }

    private function getProjection(): PlayerListProjection
    {
        try {
            /** @var PlayerListProjection $projection */
            $projection = $this->projections->find(PlayerListProjection::ID, PlayerListProjection::class);
            return $projection;
        } catch (ProjectionNotFoundException $e) {
            return new PlayerListProjection();
        }
    }
}
