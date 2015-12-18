<?php

namespace TreeHouse\Queue\Amqp;

use TreeHouse\Queue\Exception\ChannelException;
use TreeHouse\Queue\Exception\ConnectionException;

interface ChannelInterface
{
    /**
     * Get the connection in use.
     *
     * @return ConnectionInterface
     */
    public function getConnection();

    /**
     * Return internal channel ID.
     *
     * @return int
     */
    public function getChannelId();

    /**
     * Set the Quality Of Service settings for the given channel.
     *
     * @param int $prefetchSize  The window size, in octets, to prefetch.
     * @param int $prefetchCount The number of messages to prefetch.
     *
     * @throws ConnectionException If the connection to the broker was lost.
     *
     * @return bool true on success or false on failure.
     */
    public function basicQos($prefetchSize, $prefetchCount);

    /**
     * Start a transaction.
     *
     * @throws ConnectionException If the connection to the broker was lost.
     *
     * @return bool true on success or false on failure.
     */
    public function startTransaction();

    /**
     * Commit a pending transaction.
     *
     * @throws ChannelException    If no transaction was started prior to calling this method.
     * @throws ConnectionException If the connection to the broker was lost.
     *
     * @return bool true on success or false on failure.
     */
    public function commitTransaction();

    /**
     * Rollback a transaction.
     *
     * @throws ChannelException    If no transaction was started prior to calling this method.
     * @throws ConnectionException If the connection to the broker was lost.
     *
     * @return bool true on success or false on failure.
     */
    public function rollbackTransaction();

    /**
     * When using a decorator, you can use this to get the decorated object.
     *
     * @return mixed
     */
    public function getDelegate();
}
