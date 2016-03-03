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

        $delegate = new \AMQPChannel($connection->getDelegate());

        return new Channel($delegate, $connection);
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
        $delegate = new \AMQPExchange($channel->getDelegate());
        $delegate->setName($name);
        $delegate->setType($type);
        $delegate->setFlags(Exchange::convertToDelegateFlags($flags));
        $delegate->setArguments($args);

        return new Exchange($delegate, $channel);
    }

    /**
     * @inheritdoc
     */
    public function createQueue(ChannelInterface $channel, $name = null, $flags = null, array $args = [])
    {
        $delegate = new \AMQPQueue($channel->getDelegate());
        $delegate->setFlags(Queue::convertToDelegateFlags($flags));
        $delegate->setArguments($args);

        if (null !== $name) {
            $delegate->setName($name);
        }

        return new Queue($delegate, $channel);
    }
}
