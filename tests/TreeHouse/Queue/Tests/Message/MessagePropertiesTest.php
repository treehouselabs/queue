<?php

namespace TreeHouse\Queue\Tests\Message;

use TreeHouse\Queue\Message\MessageProperties;

class MessagePropertiesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $properties = new MessageProperties([
            'foo' => 'bar',
        ]);

        $this->assertInstanceOf(MessageProperties::class, $properties);
        $this->assertEquals(['foo' => 'bar'], $properties->toArray());
    }

    /**
     * @test
     */
    public function it_can_get_and_set()
    {
        $properties = new MessageProperties();
        $this->assertFalse($properties->has('foo'));

        $properties->set('foo', 'bar');
        $this->assertTrue($properties->has('foo'));
        $this->assertEquals('bar', $properties->get('foo'));
    }

    /**
     * @test
     * @expectedException \OutOfBoundsException
     */
    public function it_cannot_get_a_non_existing_key()
    {
        $properties = new MessageProperties();
        $properties->get('foo');
    }

    /**
     * @test
     */
    public function it_can_remove_a_property()
    {
        $properties = new MessageProperties([
            'foo' => 'bar',
        ]);

        $this->assertTrue($properties->has('foo'));
        $properties->remove('foo');
        $this->assertFalse($properties->has('foo'));
    }

    /**
     * @test
     */
    public function it_can_use_array_access()
    {
        $properties = new MessageProperties();
        $properties['foo'] = 'bar';

        $this->assertEquals('bar', $properties['foo']);

        unset($properties['foo']);

        $this->assertFalse(isset($properties['foo']));
    }
}
