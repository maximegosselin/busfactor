<?php

declare(strict_types=1);

namespace BusFactor\PdoTransactionCommandBusMiddleware;

use BusFactor\CommandBus\CommandDispatcherInterface;
use BusFactor\CommandBus\MiddlewareInterface;
use BusFactor\Pdo\PdoInterface;
use RuntimeException;
use Throwable;

final class PdoTransactionCommandBusMiddleware implements MiddlewareInterface
{
    private PdoInterface $pdo;

    private int $nestedLevels;

    /** @var string[] */
    private array $safeExceptions;

    /** @var Throwable[] */
    private array $deferredExceptions = [];

    public function __construct(PdoInterface $pdo, string ...$safeExceptions)
    {
        $this->pdo = $pdo;
        $this->nestedLevels = 0;
        $this->safeExceptions = $safeExceptions;
    }

    public function dispatch(object $command, CommandDispatcherInterface $next): void
    {
        $this->nestedLevels++;
        if ($this->nestedLevels === 1) {
            $this->startTransaction();
        }
        try {
            $next->dispatch($command);
        } catch (Throwable $t) {
            if (in_array(get_class($t), $this->safeExceptions)) {
                $this->deferredExceptions[] = $t;
            } else {
                $this->rollbackTransaction();
                throw $t;
            }
        }
        $this->nestedLevels--;
        if ($this->nestedLevels === 0) {
            $this->commitTransaction();
            while (!empty($this->deferredExceptions)) {
                throw array_shift($this->deferredExceptions);
            }
        }
    }

    private function startTransaction(): void
    {
        if (!$this->pdo->inTransaction()) {
            $this->pdo->beginTransaction();
        } else {
            throw new RuntimeException('Transaction already started.');
        }
    }

    private function rollbackTransaction(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }

    private function commitTransaction(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->commit();
        } else {
            throw new RuntimeException('Not in transaction.');
        }
    }
}
