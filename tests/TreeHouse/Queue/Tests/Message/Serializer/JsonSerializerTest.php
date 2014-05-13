<?php

namespace TreeHouse\Queue\Tests\Message\Serializer;

use TreeHouse\Queue\Message\Serializer\JsonSerializer;

class JsonSerializerTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $serializer = new JsonSerializer();

        $this->assertInstanceOf(JsonSerializer::class, $serializer);
    }

    /**
     * @dataProvider getTestData
     *
     * @param mixed  $value
     * @param string $expected
     */
    public function testSerialize($value, $expected)
    {
        $serializer = new JsonSerializer();
        $this->assertEquals($expected, $serializer->serialize($value));
    }

    public function getTestData()
    {
        return [
            ['foo', 'foo'], // don't serialize strings
            [['foo'], '["foo"]'],
            [false, 'false'],
            [null, 'null'],
            [new \ArrayObject(), '{}', []],
            [new \stdClass(), '{}', []],
            [1234, '1234'],
            [1234.5678, '1234.5678'],
        ];
    }
}
