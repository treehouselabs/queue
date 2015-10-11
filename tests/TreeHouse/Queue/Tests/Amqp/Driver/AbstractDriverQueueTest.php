<?php

namespace TreeHouse\Queue\Tests\Amqp\Driver;

use TreeHouse\Queue\Amqp\ChannelInterface;
use TreeHouse\Queue\Amqp\QueueInterface;

abstract class AbstractDriverQueueTest extends \PHPUnit_Framework_TestCase
{
    use DriverTestTrait;

    /**
     * @test
     */
    public function it_can_be_created()
    {
        $name = 'test';
        $flags = QueueInterface::DURABLE;
        $args = ['foo' => 'bar'];

        $conn = $this->factory->createConnection('localhost');
        $channel = $this->factory->createChannel($conn);
        $queue = $this->factory->createQueue($channel, $name, $flags, $args);

        $this->assertInstanceOf(QueueInterface::class, $queue);
        $this->assertSame($channel, $queue->getChannel());
        $this->assertSame($conn, $queue->getConnection());
        $this->assertEquals($name, $queue->getName());
        $this->assertEquals($flags, $queue->getFlags());
        $this->assertTrue($queue->hasArgument('foo'));
        $this->assertEquals('bar', $queue->getArgument('foo'));
        $this->assertEquals($args, $queue->getArguments());
    }

    /**
     * @test
     */
    public function it_can_get_and_set()
    {
        $conn = $this->factory->createConnection('localhost');
        $channel = $this->factory->createChannel($conn);
        $queue = $this->factory->createQueue($channel);

        $this->assertTrue($queue->setName($name = 'foo'));
        $this->assertSame($name, $queue->getName());

        $this->assertTrue($queue->setFlags($flags = $queue::DURABLE | $queue::PASSIVE));
        $this->assertSame($flags, $queue->getFlags());

        $this->assertTrue($queue->setArgument($key = 'foo', $value = 'bar'));
        $this->assertSame($value, $queue->getArgument($key));

        $this->assertTrue($queue->setArguments($args = ['foo' => 'bar']));
        $this->assertSame($args, $queue->getArguments());
    }

    /**
     * @return array
     */
    protected function getFlags()
    {
        return [
            QueueInterface::NOPARAM,
            QueueInterface::DURABLE,
            QueueInterface::PASSIVE,
            QueueInterface::EXCLUSIVE,
            QueueInterface::AUTODELETE,
            QueueInterface::MULTIPLE,
            QueueInterface::AUTOACK,
            QueueInterface::REQUEUE,
            QueueInterface::IFUNUSED,
            QueueInterface::IFEMPTY,
        ];
    }

    /**
     * @param ChannelInterface $channel
     *
     * @return mixed
     */
    abstract protected function getDelegate(ChannelInterface $channel);
}
