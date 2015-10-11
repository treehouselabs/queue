<?php

namespace TreeHouse\Queue\Tests\Amqp\Driver\Amqp;

use TreeHouse\Queue\Amqp\ConnectionInterface;
use TreeHouse\Queue\Tests\Amqp\Driver\AbstractDriverChannelTest;

class ChannelTest extends AbstractDriverChannelTest
{
    use AmqpFactoryTestTrait;

    /**
     * @inheritdoc
     */
    protected function getDelegate(ConnectionInterface $conn)
    {
        return new \AMQPChannel($conn->getDelegate());
    }
}
