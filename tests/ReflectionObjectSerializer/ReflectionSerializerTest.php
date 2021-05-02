<?php

declare(strict_types=1);

namespace BusFactor\Test\ReflectionObjectSerializer;

use BusFactor\ObjectSerializer\ObjectSerializer;
use BusFactor\ObjectSerializer\ReflectionObjectSerializer;
use PHPUnit\Framework\TestCase;

class ReflectionSerializerTest extends TestCase
{
    /** @test */
    public function it_serializes_and_unserializes_objects(): void
    {
        $serializer = new ObjectSerializer(new ReflectionObjectSerializer());

        $object = new TestClass();

        $string = $serializer->serialize($object);
        $newObject = $serializer->deserialize($string);

        $this->assertEquals($object, $newObject);
    }
}
