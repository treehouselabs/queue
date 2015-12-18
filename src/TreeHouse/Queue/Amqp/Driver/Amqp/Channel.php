<?php

namespace TreeHouse\Queue\Amqp\Driver\Amqp;

use TreeHouse\Queue\Amqp\ChannelInterface;
use TreeHouse\Queue\Amqp\ConnectionInterface;
use TreeHouse\Queue\Exception\ChannelException;
use TreeHouse\Queue\Exception\ConnectionException;

class Channel implements ChannelInterface
{
    /**
     * @var \AMQPChannel
     */
    protected $delegate;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @param \AMQPChannel        $delegate
     * @param ConnectionInterface $connection
     */
    public function __construct(\AMQPChannel $delegate, ConnectionInterface &$connection)
    {
        $this->delegate = $delegate;
        $this->connection = &$connection;
    }

    /**
     * @inheritdoc
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @inheritdoc
     */
    public function getChannelId()
    {
        return $this->delegate->getChannelId();
    }

    /**
     * @inheritdoc
     */
    public function basicQos($prefetchSize, $prefetchCount)
    {
        try {
            return $this->delegate->qos($prefetchSize, $prefetchCount);
        } catch (\AMQPChannelException $e) {
            throw new ChannelException($e->getMessage(), $e->getCode(), $e);
        } catch (\AMQPConnectionException $e) {
            throw new ConnectionException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function startTransaction()
    {
        try {
            return $this->delegate->startTransaction();
        } catch (\AMQPChannelException $e) {
            throw new ChannelException($e->getMessage(), $e->getCode(), $e);
        } catch (\AMQPConnectionException $e) {
            throw new ConnectionException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function commitTransaction()
    {
        try {
            return $this->delegate->commitTransaction();
        } catch (\AMQPChannelException $e) {
            throw new ChannelException($e->getMessage(), $e->getCode(), $e);
        } catch (\AMQPConnectionException $e) {
            throw new ConnectionException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function rollbackTransaction()
    {
        try {
            return $this->delegate->rollbackTransaction();
        } catch (\AMQPChannelException $e) {
            throw new ChannelException($e->getMessage(), $e->getCode(), $e);
        } catch (\AMQPConnectionException $e) {
            throw new ConnectionException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @inheritdoc
     *
     * @return \AMQPChannel
     */
    public function getDelegate()
    {
        return $this->delegate;
    }
}
