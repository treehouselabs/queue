<?php

namespace TreeHouse\Queue\Tests\Amqp\Driver\Amqp;

use TreeHouse\Queue\Amqp\ChannelInterface;
use TreeHouse\Queue\Amqp\Driver\Amqp\Queue;
use TreeHouse\Queue\Tests\Amqp\Driver\AbstractDriverQueueTest;

class QueueTest extends AbstractDriverQueueTest
{
    use AmqpFactoryTestTrait;

    /**
     * @inheritdoc
     */
    protected function getDelegate(ChannelInterface $channel)
    {
        return new \AMQPQueue($channel->getDelegate());
    }

    /**
     * @test
     */
    public function flags_are_converted()
    {
        foreach ($this->getFlags() as $flag) {
            $this->assertSame($flag, Queue::convertFromDelegateFlags(Queue::convertToDelegateFlags($flag)));
        }
    }
}
