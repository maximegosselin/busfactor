<?php

declare(strict_types=1);

namespace BusFactor\Test\Example\Command;

use BusFactor\CommandDispatcher\HandleCommandTrait;
use BusFactor\CommandDispatcher\HandlerInterface;
use BusFactor\Example\Aggregate\Player;
use BusFactor\Example\Aggregate\PlayerRepository;

class PlayerCommandHandler implements HandlerInterface
{
    use HandleCommandTrait;

    private PlayerRepository $players;

    public function __construct(PlayerRepository $players)
    {
        $this->players = $players;
    }

    public static function getHandledCommandClasses(): array
    {
        return [
            RegisterPlayerCommand::class,
            ChangePlayerNameCommand::class,
        ];
    }

    private function handleRegisterPlayerCommand(RegisterPlayerCommand $command): void
    {
        $player = Player::register($command->getId(), $command->getNumber(), $command->getName());
        $this->players->store($player);
    }

    private function handleChangePlayerNameCommand(ChangePlayerNameCommand $command): void
    {
        $player = $this->players->find($command->getId());
        $player->changeName($command->getName());
        $this->players->store($player);
    }
}
