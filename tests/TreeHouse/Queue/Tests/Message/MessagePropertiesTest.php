<?php

namespace TreeHouse\Queue\Tests;

use TreeHouse\Queue\Message\MessageProperties;

class MessagePropertiesTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $properties = new MessageProperties([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(MessageProperties::class, $properties);
        $this->assertEquals(['foo' => 'bar'], $properties->toArray());
    }

    public function testGetSet()
    {
        $properties = new MessageProperties();
        $this->assertFalse($properties->has('foo'));

        $properties->set('foo', 'bar');
        $this->assertTrue($properties->has('foo'));
        $this->assertEquals('bar', $properties->get('foo'));
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGetNonExistingKey()
    {
        $properties = new MessageProperties();
        $properties->get('foo');
    }

    public function testRemove()
    {
        $properties = new MessageProperties([
            'foo' => 'bar'
        ]);

        $this->assertTrue($properties->has('foo'));
        $properties->remove('foo');
        $this->assertFalse($properties->has('foo'));
    }

    public function testArrayAccess()
    {
        $properties = new MessageProperties();
        $properties['foo'] = 'bar';

        $this->assertEquals('bar', $properties['foo']);

        unset($properties['foo']);

        $this->assertFalse(isset($properties['foo']));
    }

    public function testSpecialGetters()
    {
        $properties = new MessageProperties([
            'content_type'  => MessageProperties::CONTENT_TYPE_TEXT_PLAIN,
            'delivery_mode' => MessageProperties::DELIVERY_MODE_NON_PERSISTENT,
        ]);

        $this->assertSame(MessageProperties::CONTENT_TYPE_TEXT_PLAIN, $properties->getContentType());
        $this->assertSame(MessageProperties::DELIVERY_MODE_NON_PERSISTENT, $properties->getDeliveryMode());
    }
}
