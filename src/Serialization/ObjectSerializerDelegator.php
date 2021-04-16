<?php

declare(strict_types=1);

namespace BusFactor\Serialization;

final class ObjectSerializerDelegator implements ObjectSerializerInterface
{
    private MiddlewareInterface $middleware;

    private ?ObjectSerializerInterface $next = null;

    public function __construct(MiddlewareInterface $middleware, ?ObjectSerializerInterface $next)
    {
        $this->middleware = $middleware;
        $this->next = $next;
    }

    public function serialize(object $object): string
    {
        return $this->middleware->serialize($object, $this->next);
    }

    public function deserialize(string $payload): object
    {
        return $this->middleware->deserialize($payload, $this->next);
    }
}
