<?php

namespace TreeHouse\Queue\Tests\Message\Composer;

use TreeHouse\Queue\Message\Composer\DefaultMessageComposer;
use TreeHouse\Queue\Message\Message;
use TreeHouse\Queue\Message\MessageProperties;
use TreeHouse\Queue\Message\Serializer\JsonSerializer;
use TreeHouse\Queue\Message\Serializer\PhpSerializer;

class DefaultMessageComposerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     *
     * @return DefaultMessageComposer
     */
    public function it_can_be_constructed()
    {
        $composer = new DefaultMessageComposer(new PhpSerializer());
        $this->assertInstanceOf(DefaultMessageComposer::class, $composer);

        return $composer;
    }

    /**
     * @test
     * @depends it_can_be_constructed
     *
     * @param DefaultMessageComposer $composer
     */
    public function it_can_compose_messages(DefaultMessageComposer $composer)
    {
        $id = 'msgid';
        $body = 'test';
        $route = 'foo_route';
        $properties = ['foo' => 'bar'];

        $message = $composer->compose($body, $properties, $id, $route);

        $this->assertInstanceOf(Message::class, $message);
        $this->assertInstanceOf(MessageProperties::class, $message->getProperties());

        $this->assertEquals($body, $message->getBody());
        $this->assertEquals($id, $message->getId());
        $this->assertEquals($route, $message->getRoutingKey());
        $this->assertTrue($message->getProperties()->has('foo'));
        $this->assertEquals('bar', $message->getProperties()->get('foo'));
    }

    /**
     * @test
     */
    public function it_serializes_messages()
    {
        $composer = new DefaultMessageComposer(new JsonSerializer());
        $message = $composer->compose(['test']);

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals('["test"]', $message->getBody());
        $this->assertInstanceOf(MessageProperties::class, $message->getProperties());
    }
}
