<?php

namespace TreeHouse\Queue\Amqp\Driver\Amqp;

use TreeHouse\Queue\Amqp\ChannelInterface;
use TreeHouse\Queue\Amqp\ConnectionInterface;
use TreeHouse\Queue\Amqp\ExchangeInterface;
use TreeHouse\Queue\Factory\FactoryInterface;

class AmqpFactory implements FactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createConnection($host, $port = 5672, $user = 'guest', $pass = 'guest', $vhost = '/')
    {
        $delegate = new \AMQPConnection(
            [
                'host' => $host,
                'port' => $port,
                'login' => $user,
                'password' => $pass,
                'vhost' => $vhost,
            ]
        );

        return new Connection($delegate);
    }

    /**
     * @inheritdoc
     */
    public function createChannel(ConnectionInterface $connection)
    {
        // we have to be connected before instantiating a channel
        if (!$connection->isConnected()) {
            $connection->connect();
        }

        $channel = new \AMQPChannel($connection->getDelegate());

        return new Channel($channel, $connection);
    }

    /**
     * @inheritdoc
     */
    public function createExchange(
        ChannelInterface $channel,
        $name,
        $type = ExchangeInterface::TYPE_DIRECT,
        $flags = null,
        array $args = []
    ) {
        $exchange = new \AMQPExchange($channel->getDelegate());
        $exchange->setName($name);
        $exchange->setType($type);
        $exchange->setFlags(Exchange::convertToDelegateFlags($flags));
        $exchange->setArguments($args);

        $exchange->declareExchange();

        return new Exchange($exchange, $channel);
    }

    /**
     * @inheritdoc
     */
    public function createQueue(ChannelInterface $channel, $name = null, $flags = null, array $args = [])
    {
        $queue = new \AMQPQueue($channel->getDelegate());
        $queue->setFlags(Queue::convertToDelegateFlags($flags));
        $queue->setArguments($args);

        if (null !== $name) {
            $queue->setName($name);
        }

        $queue->declareQueue();

        return new Queue($queue, $channel);
    }
}
