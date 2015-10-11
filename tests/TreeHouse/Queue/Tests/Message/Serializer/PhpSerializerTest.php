<?php

namespace TreeHouse\Queue\Tests\Message\Serializer;

use TreeHouse\Queue\Message\Serializer\PhpSerializer;

class PhpSerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $serializer = new PhpSerializer();

        $this->assertInstanceOf(PhpSerializer::class, $serializer);
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
        $serializer = new PhpSerializer();
        $this->assertEquals($expected, $serializer->serialize($value));
    }

    /**
     * @return array
     */
    public function getTestData()
    {
        return [
            ['foo', 'foo'], // don't serialize strings
            [['foo'], 'a:1:{i:0;s:3:"foo";}'],
            [false, 'b:0;'],
            [null, 'N;'],
            [new \ArrayObject(), 'C:11:"ArrayObject":21:{x:i:0;a:0:{};m:a:0:{}}'],
            [1234, 'i:1234;'],
            [1234.5678, 'd:1234.5678;'],
        ];
    }
}
