<?php

namespace TreeHouse\Queue\Tests\Message\Provider;

use TreeHouse\Queue\Amqp\EnvelopeInterface;
use TreeHouse\Queue\Amqp\QueueInterface;
use TreeHouse\Queue\Message\Message;
use TreeHouse\Queue\Message\Provider\MessageProvider;

class AmqpMessageProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var QueueInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $queue;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->queue = $this
            ->getMockBuilder(QueueInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $provider = new MessageProvider($this->queue);

        $this->assertInstanceOf(MessageProvider::class, $provider);
    }

    /**
     * @test
     */
    public function it_can_get_a_message()
    {
        $id = uniqid();
        $body = 'test';
        $headers = ['foo' => 'bar'];

        $envelope = $this->getMock(EnvelopeInterface::class);
        $envelope->expects($this->once())->method('getDeliveryTag')->will($this->returnValue($id));
        $envelope->expects($this->once())->method('getBody')->will($this->returnValue($body));
        $envelope->expects($this->once())->method('getHeaders')->will($this->returnValue($headers));

        $this->queue
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($envelope))
        ;

        $provider = new MessageProvider($this->queue);
        $message = $provider->get();

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals($id, $message->getId());
        $this->assertEquals($body, $message->getBody());
        $this->assertEquals($headers, $message->getProperties()->toArray());
    }

    /**
     * @test
     */
    public function it_can_consume_a_message()
    {
        $id      = uniqid();
        $body    = 'test';
        $headers = ['foo' => 'bar'];

        $envelope = $this->getMock(EnvelopeInterface::class);
        $envelope->expects($this->any())->method('getDeliveryTag')->will($this->returnValue($id));
        $envelope->expects($this->any())->method('getBody')->will($this->returnValue($body));
        $envelope->expects($this->any())->method('getHeaders')->will($this->returnValue($headers));

        $callback = function($message) use ($id, $body, $headers) {
            /** @var Message $message */
            $this->assertInstanceOf(Message::class, $message);

            $this->assertEquals($id, $message->getId());
            $this->assertEquals($body, $message->getBody());
            $this->assertEquals($headers, $message->getProperties()->toArray());
        };

        $this->queue
            ->expects($this->once())
            ->method('consume')
            ->with($this->callback(function($callback) use ($envelope) {
                $callback($envelope);

                return true;
            }))
        ;

        $provider = new MessageProvider($this->queue);
        $provider->consume($callback);
    }

    /**
     * @test
     */
    public function it_returns_null_on_empty_envelope()
    {
        $this->queue
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue(false))
        ;

        $provider = new MessageProvider($this->queue);

        $this->assertNull($provider->get());
    }

    /**
     * @test
     */
    public function it_can_ack_a_message()
    {
        $id = uniqid();
        $body = 'test';
        $message = new Message($body, null, $id);

        $this->queue
            ->expects($this->once())
            ->method('ack')
            ->with($id)
        ;

        $provider = new MessageProvider($this->queue);
        $provider->ack($message);
    }

    /**
     * @test
     */
    public function it_can_nack_a_message()
    {
        $id = uniqid();
        $body = 'test';
        $message = new Message($body, null, $id);

        $this->queue
            ->expects($this->once())
            ->method('nack')
            ->with($id, null)
        ;

        $provider = new MessageProvider($this->queue);
        $provider->nack($message);
    }

    /**
     * @test
     */
    public function it_can_nack_and_requeue_a_message()
    {
        $id = uniqid();
        $body = 'test';
        $message = new Message($body, null, $id);

        $this->queue
            ->expects($this->once())
            ->method('nack')
            ->with($id, QueueInterface::REQUEUE)
        ;

        $provider = new MessageProvider($this->queue);
        $provider->nack($message, true);
    }
}
