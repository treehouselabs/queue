<?php

namespace TreeHouse\Queue\Tests\Message;

use TreeHouse\Queue\Message\Message;
use TreeHouse\Queue\Message\MessageProperties;

class MessageTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $body    = 'test';
        $message = new Message($body);

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals($body, $message->getBody());
    }

    public function testArguments()
    {
        $id    = uniqid();
        $body  = 'test';
        $route = 'foo_route';
        $props = [
            'foo' => 'bar',
        ];

        $properties = new MessageProperties($props);
        $message    = new Message($body, $properties, $id, $route);

        $this->assertEquals($id, $message->getId());
        $this->assertEquals($body, $message->getBody());
        $this->assertEquals($route, $message->getRoutingKey());
        $this->assertTrue($message->getProperties()->has('foo'));
        $this->assertEquals('bar', $message->getProperties()->get('foo'));
    }

    public function testGettersAndSetters()
    {
        $props = new MessageProperties(['foo' => 'bar']);

        $message = new Message('test');
        $message->setBody('test2');
        $message->setRoutingKey('route2');
        $message->setProperties($props);

        $this->assertEquals('test2', $message->getBody());
        $this->assertEquals('route2', $message->getRoutingKey());
        $this->assertSame($props, $message->getProperties());
    }

    public function testContentType()
    {
        $message = new Message('test');
        $message->setContentType('text/xml');

        $this->assertEquals('text/xml', $message->getContentType());
    }

    public function testDeliveryMode()
    {
        $message = new Message('test');
        $message->setDeliveryMode(3);

        $this->assertEquals(3, $message->getDeliveryMode());
    }

    public function testPriority()
    {
        $message = new Message('test');
        $message->setPriority(3);

        $this->assertEquals(3, $message->getPriority());
    }
}
