<?php

declare(strict_types=1);

namespace BusFactor\Test\Example\Aggregate;

use BusFactor\Aggregate\EventSourcedAggregateInterface;
use BusFactor\Aggregate\EventSourcedAggregateRootTrait;

class Player implements EventSourcedAggregateInterface
{
    use EventSourcedAggregateRootTrait;

    private int $number;

    private string $name;

    private int $points;

    public static function getType(): string
    {
        return 'player';
    }

    public static function register(string $id, int $number, string $name): self
    {
        $me = new static($id);
        $me->apply(new PlayerRegisteredEvent($number, $name));
        return $me;
    }

    public function changeName(string $name): void
    {
        if ($name !== $this->name) {
            $this->apply(new PlayerNameChangedEvent($this->name, $name));
        }
    }

    private function applyPlayerRegisteredEvent(PlayerRegisteredEvent $event): void
    {
        $this->number = $event->getNumber();
        $this->name = $event->getName();
        $this->points = 0;
    }

    private function applyPlayerNameChangedEvent(PlayerNameChangedEvent $event): void
    {
        $this->name = $event->getNew();
    }
}
