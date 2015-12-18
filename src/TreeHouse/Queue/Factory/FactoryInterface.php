<?php

namespace TreeHouse\Queue\Factory;

use TreeHouse\Queue\Amqp\ChannelInterface;
use TreeHouse\Queue\Amqp\ConnectionInterface;
use TreeHouse\Queue\Amqp\ExchangeInterface;
use TreeHouse\Queue\Amqp\QueueInterface;

interface FactoryInterface
{
    /**
     * @param string $host
     * @param int    $port
     * @param string $user
     * @param string $pass
     * @param string $vhost
     *
     * @return ConnectionInterface
     */
    public function createConnection($host, $port = 5672, $user = 'guest', $pass = 'guest', $vhost = '/');

    /**
     * @param ConnectionInterface $connection
     *
     * @return ChannelInterface
     */
    public function createChannel(ConnectionInterface $connection);

    /**
     * @param ChannelInterface $channel
     * @param string           $name
     * @param string           $type
     * @param int              $flags
     * @param array            $args
     *
     * @return ExchangeInterface
     */
    public function createExchange(
        ChannelInterface $channel,
        $name,
        $type = ExchangeInterface::TYPE_DIRECT,
        $flags = null,
        array $args = []
    );

    /**
     * @param ChannelInterface $channel
     * @param string           $name
     * @param int              $flags
     * @param array            $args
     *
     * @return QueueInterface
     */
    public function createQueue(ChannelInterface $channel, $name = null, $flags = null, array $args = []);
}
