<?php

namespace TreeHouse\Queue\Tests\Message\Provider;

use TreeHouse\Queue\Message\Message;
use TreeHouse\Queue\Message\Provider\AmqpMessageProvider;

class AmqpMessageProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \AMQPQueue|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $queue;

    public function testConstructor()
    {
        $provider = new AmqpMessageProvider($this->queue);

        $this->assertInstanceOf(AmqpMessageProvider::class, $provider);
    }

    public function testGet()
    {
        $id      = uniqid();
        $body    = 'test';
        $headers = ['foo' => 'bar'];

        $envelope = $this->getMock(\AMQPEnvelope::class);
        $envelope->expects($this->once())->method('getDeliveryTag')->will($this->returnValue($id));
        $envelope->expects($this->once())->method('getBody')->will($this->returnValue($body));
        $envelope->expects($this->once())->method('getHeaders')->will($this->returnValue($headers));

        $this->queue
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($envelope))
        ;

        $provider = new AmqpMessageProvider($this->queue);
        $message = $provider->get();

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals($id, $message->getId());
        $this->assertEquals($body, $message->getBody());
        $this->assertEquals($headers, $message->getProperties()->toArray());
    }

    public function testReturnNullOnEmptyEnvelope()
    {
        $this->queue
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue(false))
        ;

        $provider = new AmqpMessageProvider($this->queue);

        $this->assertNull($provider->get());
    }

    public function testAck()
    {
        $id      = uniqid();
        $body    = 'test';
        $message = new Message($body, null, $id);

        $this->queue
            ->expects($this->once())
            ->method('ack')
            ->with($id)
        ;

        $provider = new AmqpMessageProvider($this->queue);
        $provider->ack($message);
    }

    public function testNack()
    {
        $id      = uniqid();
        $body    = 'test';
        $message = new Message($body, null, $id);

        $this->queue
            ->expects($this->once())
            ->method('nack')
            ->with($id, null)
        ;

        $provider = new AmqpMessageProvider($this->queue);
        $provider->nack($message);
    }

    public function testNackAndRequeue()
    {
        $id      = uniqid();
        $body    = 'test';
        $message = new Message($body, null, $id);

        $this->queue
            ->expects($this->once())
            ->method('nack')
            ->with($id, AMQP_REQUEUE)
        ;

        $provider = new AmqpMessageProvider($this->queue);
        $provider->nack($message, true);
    }

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->queue = $this
            ->getMockBuilder(\AmqpQueue::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }
}
