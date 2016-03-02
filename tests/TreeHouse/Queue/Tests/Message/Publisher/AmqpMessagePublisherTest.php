<?php

namespace TreeHouse\Queue\Tests\Message\Publisher;

use Mockery as Mock;
use Mockery\MockInterface;
use TreeHouse\Queue\Amqp\Driver\Amqp\Publisher\AmqpMessagePublisher;
use TreeHouse\Queue\Amqp\ExchangeInterface;
use TreeHouse\Queue\Message\Composer\DefaultMessageComposer;
use TreeHouse\Queue\Message\Composer\MessageComposerInterface;
use TreeHouse\Queue\Message\Message;
use TreeHouse\Queue\Message\MessageProperties;
use TreeHouse\Queue\Message\Publisher\MessagePublisherInterface;
use TreeHouse\Queue\Message\Serializer\PhpSerializer;

class AmqpMessagePublisherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ExchangeInterface|MockInterface
     */
    protected $exchange;

    /**
     * @var MessageComposerInterface
     */
    protected $composer;

    /**
     * @var MessagePublisherInterface
     */
    protected $publisher;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->exchange = Mock::mock(ExchangeInterface::class);
        $this->composer = new DefaultMessageComposer(new PhpSerializer());
        $this->publisher = new AmqpMessagePublisher($this->exchange, $this->composer);
    }

    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf(MessagePublisherInterface::class, $this->publisher);
    }

    /**
     * @test
     */
    public function it_can_create_a_message()
    {
        $body = 'test';
        $message = $this->publisher->createMessage($body);

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals($body, $message->getBody());
        $this->assertEquals(MessageProperties::CONTENT_TYPE_TEXT_PLAIN, $message->getContentType());
        $this->assertEquals(MessageProperties::DELIVERY_MODE_PERSISTENT, $message->getDeliveryMode());
    }

    /**
     * @test
     */
    public function it_can_create_a_message_with_priority()
    {
        $message = $this->publisher->createMessage('test', 4);

        $this->assertEquals(4, $message->getPriority());
    }

    /**
     * @test
     */
    public function it_can_publish_a_message()
    {
        $body = 'test';
        $message = $this->publisher->createMessage($body);

        $this->exchange
            ->shouldReceive('publish')
            ->once()
            ->with($body, null, ExchangeInterface::NOPARAM, $message->getProperties()->toArray())
            ->andReturn(true)
        ;

        $this->assertTrue($this->publisher->publish($message));
    }

    /**
     * @test
     */
    public function it_can_publish_a_message_with_arguments()
    {
        $body = 'test';
        $route = 'foo_route';
        $message = $this->publisher->createMessage($body);
        $message->setRoutingKey($route);

        $this->exchange
            ->shouldReceive('publish')
            ->once()
            ->with($body, $route, ExchangeInterface::IMMEDIATE, $message->getProperties()->toArray())
            ->andReturn(true)
        ;

        $this->assertTrue($this->publisher->publish($message, null, ExchangeInterface::IMMEDIATE));
    }

    /**
     * @test
     */
    public function it_can_publish_a_message_with_a_delay()
    {
        $body = 'test';
        $date = new \DateTime('+10 seconds');
        $message = $this->publisher->createMessage($body);

        $props = $message->getProperties()->toArray();
        $props[MessageProperties::KEY_HEADERS] = [
            MessageProperties::KEY_DELAY => 10000
        ];

        $this->exchange
            ->shouldReceive('publish')
            ->once()
            ->with($body, null, ExchangeInterface::NOPARAM, $props)
            ->andReturn(true)
        ;

        $this->assertTrue($this->publisher->publish($message, $date));
    }

    /**
     * @test
     * @expectedException        \OutOfBoundsException
     * @expectedExceptionMessage You cannot publish a message in the past
     */
    public function it_cannot_publish_a_message_in_the_past()
    {
        $message = $this->publisher->createMessage('test');

        $this->exchange->shouldReceive('publish')->never();

        $this->publisher->publish($message, new \DateTime('-10 minutes'));
    }
}
