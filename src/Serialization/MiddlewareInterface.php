<?php

declare(strict_types=1);

namespace BusFactor\Serialization;

interface MiddlewareInterface
{
    public function serialize(object $object, ObjectSerializerInterface $next): string;

    public function deserialize(string $payload, ObjectSerializerInterface $next): object;
}
