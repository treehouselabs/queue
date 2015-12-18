<?php

namespace TreeHouse\Queue\Tests\Message\Serializer;

use TreeHouse\Queue\Message\Serializer\JsonSerializer;

class JsonSerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $serializer = new JsonSerializer();

        $this->assertInstanceOf(JsonSerializer::class, $serializer);
    }

    /**
     * @test
     * @dataProvider getTestData
     *
     * @param mixed  $value
     * @param string $expected
     */
    public function it_can_serialize($value, $expected)
    {
        $serializer = new JsonSerializer();
        $this->assertEquals($expected, $serializer->serialize($value));
    }

    /**
     * @return array
     */
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
