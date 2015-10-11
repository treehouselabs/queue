<?php

namespace TreeHouse\Queue\Tests\Amqp\Driver\Amqp;

use TreeHouse\Queue\Amqp\ChannelInterface;
use TreeHouse\Queue\Amqp\ConnectionInterface;
use TreeHouse\Queue\Tests\Amqp\Driver\AbstractDriverEnvelopeTest;

class EnvelopeTest extends AbstractDriverEnvelopeTest
{
    use AmqpFactoryTestTrait;

    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * @var ChannelInterface
     */
    private $channel;

    /**
     * @inheritdoc
     */
    protected function getExchange()
    {
        $conn = $this->getConnection();
        $channel = $this->getChannel($conn);

        return $this->factory->createExchange($channel, 'test');
    }

    /**
     * @inheritdoc
     */
    protected function getQueue()
    {
        $conn = $this->getConnection();
        $channel = $this->getChannel($conn);

        return $this->factory->createQueue($channel);
    }

    /**
     * @return ConnectionInterface
     */
    protected function getConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->factory->createConnection('localhost');
        }

        return $this->connection;
    }

    /**
     * @param ConnectionInterface $conn
     *
     * @return ChannelInterface
     */
    protected function getChannel(ConnectionInterface $conn)
    {
        if (!$this->channel) {
            $this->channel = $this->factory->createChannel($conn);
        }

        return $this->channel;
    }
}
