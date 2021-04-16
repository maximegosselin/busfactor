<?php

declare(strict_types=1);

namespace BusFactor\Aggregate;

use ReflectionClass;
use RuntimeException;

trait SerializationTrait
{
    public static function deserialize(array $data): EventInterface
    {
        $class = new ReflectionClass(__CLASS__);

        $args = [];
        $constructor = $class->getConstructor();
        if ($constructor) {
            foreach ($constructor->getParameters() as $parameter) {
                $paramName = $parameter->getName();
                if (!array_key_exists($paramName, $data)) {
                    throw new RuntimeException(
                        sprintf(
                            "Deserialization error: No payload value for the constructor argument named '%s'.",
                            $paramName
                        )
                    );
                }
                $args[] = $data[$paramName];
            }
        }

        /** @var EventInterface $object */
        $object = $class->newInstanceArgs($args);
        return $object;
    }

    public function serialize(): array
    {
        return get_object_vars($this);
    }
}
