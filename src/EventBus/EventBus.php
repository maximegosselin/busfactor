<?php

declare(strict_types=1);

namespace BusFactor\EventBus;

use BusFactor\EventStream\Stream;

final class EventBus implements EventBusInterface
{
    private Dispatcher $dispatcher;

    /** @var MiddlewareInterface[] */
    private array $middlewares = [];

    private ?EventStreamPublisherInterface $chain = null;

    public function __construct()
    {
        $this->dispatcher = new Dispatcher();
        $this->middlewares = [];
        $this->chainMiddlewares();
    }

    public function addMiddleware(MiddlewareInterface $middleware): void
    {
        $this->middlewares[] = $middleware;
        $this->chainMiddlewares();
    }

    /** @return MiddlewareInterface[] */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    public function publish(Stream $stream): void
    {
        if (!$this->chain) {
            $this->chainMiddlewares();
        }
        $this->chain->publish($stream);
    }

    public function subscribe(string $eventClass, EventHandlerInterface $subscriber): void
    {
        $this->dispatcher->subscribe($eventClass, $subscriber);
    }

    private function chainMiddlewares(): void
    {
        $this->chain = array_reduce(
            $this->middlewares,
            function (EventStreamPublisherInterface $carry, MiddlewareInterface $item): EventStreamPublisherInterface {
                return new EventStreamPublisherDelegator($item, $carry);
            },
            $this->dispatcher
        );
    }
}
