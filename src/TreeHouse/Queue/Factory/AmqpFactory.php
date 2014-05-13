<?php

namespace TreeHouse\Queue\Factory;

class AmqpFactory
{
    /**
     * @param string  $host
     * @param integer $port
     * @param string  $user
     * @param string  $pass
     * @param string  $vhost
     *
     * @return \AMQPConnection
     */
    public function createConnection($host, $port = 5672, $user = 'guest', $pass = 'guest', $vhost = '/')
    {
        return new \AMQPConnection(
            [
                'host'     => $host,
                'port'     => $port,
                'login'    => $user,
                'password' => $pass,
                'vhost'    => $vhost,
            ]
        );
    }

    /**
     * @param \AMQPConnection $connection
     *
     * @return \AMQPChannel
     */
    public function createChannel(\AMQPConnection $connection)
    {
        // we have to be connected before instantiating a channel
        if (!$connection->isConnected()) {
            $connection->connect();
        }

        return new \AMQPChannel($connection);
    }

    /**
     * @param \AMQPChannel $channel
     * @param string       $name
     * @param string       $type
     * @param integer      $flags
     * @param array        $args
     *
     * @return \AMQPExchange
     */
    public function createExchange(
        \AMQPChannel $channel,
        $name,
        $type = AMQP_EX_TYPE_DIRECT,
        $flags = AMQP_NOPARAM,
        array $args = []
    ) {
        $exchange = new \AMQPExchange($channel);
        $exchange->setName($name);
        $exchange->setType($type);
        $exchange->setFlags($flags);
        $exchange->setArguments($args);

        $exchange->declareExchange();

        return $exchange;
    }

    /**
     * @param \AMQPChannel $channel
     * @param string       $name
     * @param integer      $flags
     * @param array        $args
     *
     * @return \AMQPQueue
     */
    public function createQueue(\AMQPChannel $channel, $name, $flags = AMQP_NOPARAM, array $args = [])
    {
        $queue = new \AMQPQueue($channel);
        $queue->setName($name);
        $queue->setFlags($flags);
        $queue->setArguments($args);

        $queue->declareQueue();

        return $queue;
    }
}
