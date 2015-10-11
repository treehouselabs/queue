<?php

namespace TreeHouse\Queue\Amqp;

use TreeHouse\Queue\Exception\ChannelException;
use TreeHouse\Queue\Exception\ConnectionException;
use TreeHouse\Queue\Exception\ExchangeException;

interface ExchangeInterface
{
    const TYPE_DIRECT = 'direct';
    const TYPE_FANOUT = 'fanout';
    const TYPE_TOPIC = 'topic';
    const TYPE_HEADERS = 'headers';

    const NOPARAM = 0;
    const DURABLE = 1;
    const PASSIVE = 2;
    const AUTODELETE = 4;
    const INTERNAL = 8;
    const IFUNUSED = 16;
    const MANDATORY = 32;
    const IMMEDIATE = 64;
    const NOWAIT = 128;

    /**
     * Get the channel in use.
     *
     * @return ChannelInterface
     */
    public function getChannel();

    /**
     * Get the connection in use.
     *
     * @return ConnectionInterface
     */
    public function getConnection();

    /**
     * Get the exchange name.
     *
     * @return string
     */
    public function getName();

    /**
     * Get the exchange type.
     *
     * @return string
     */
    public function getType();

    /**
     * Get all the flags currently set on the given exchange.
     *
     * @return int An integer bitmask of all the flags currently set on this
     *             exchange object.
     */
    public function getFlags();

    /**
     * Get the argument associated with the given key.
     *
     * @param string $key The key to look up.
     *
     * @return string|int|bool The string or integer value associated
     *                         with the given key, or FALSE if the key
     *                         is not set.
     */
    public function getArgument($key);

    /**
     * Get all arguments set on the given exchange.
     *
     * @return array An array containing all of the set key/value pairs.
     */
    public function getArguments();

    /**
     * Bind to another exchange.
     *
     * Bind an exchange to another exchange using the specified routing key.
     *
     * @param string $exchangeName Name of the exchange to bind.
     * @param string $routingKey   The routing key to use for binding.
     * @param array  $arguments    Additional binding arguments.
     *
     * @throws ExchangeException   On failure.
     * @throws ChannelException    If the channel is not open.
     * @throws ConnectionException If the connection to the broker was lost.
     *
     * @return bool true on success or false on failure.
     */
    public function bind($exchangeName, $routingKey, array $arguments = []);

    /**
     * Remove binding to another exchange.
     *
     * Remove a routing key binding on an another exchange from the given exchange.
     *
     * @param string $exchangeName Name of the exchange to unbind.
     * @param string $routingKey   The routing key to use for unbinding.
     * @param array  $arguments    Additional arguments.
     *
     * @throws ExchangeException   On failure.
     * @throws ChannelException    If the channel is not open.
     * @throws ConnectionException If the connection to the broker was lost.
     *
     * @return bool true on success or false on failure.
     */
    public function unbind($exchangeName, $routingKey, array $arguments = []);

    /**
     * Declare a new exchange on the broker.
     *
     * @throws ExchangeException   On failure.
     * @throws ChannelException    If the channel is not open.
     * @throws ConnectionException If the connection to the broker was lost.
     *
     * @return bool true on success or false on failure.
     */
    public function declareExchange();

    /**
     * Delete the exchange from the broker.
     *
     * @param string $exchangeName Optional name of exchange to delete.
     * @param int    $flags        Optionally IFUNUSED can be specified to indicate the exchange should not be deleted
     *                             until no clients are connected to it.
     *
     * @throws ExchangeException   On failure.
     * @throws ChannelException    If the channel is not open.
     * @throws ConnectionException If the connection to the broker was lost.
     *
     * @return bool true on success or false on failure.
     */
    public function delete($exchangeName = null, $flags = null);

    /**
     * Publish a message to an exchange.
     *
     * @param string $message    The message to publish.
     * @param string $routingKey The optional routing key to which to publish to.
     * @param int    $flags      One or more of MANDATORY and IMMEDIATE.
     * @param array  $attributes Available keys: content_type, content_encoding,
     *                           message_id, user_id, app_id, delivery_mode,
     *                           priority, timestamp, expiration, type, reply_to,
     *                           headers.
     *
     * @throws ExchangeException   On failure.
     * @throws ChannelException    If the channel is not open.
     * @throws ConnectionException If the connection to the broker was lost.
     *
     * @return bool true on success or false on failure.
     */
    public function publish($message, $routingKey = null, $flags = null, array $attributes = []);

    /**
     * When using a decorator, you can use this to get the decorated object.
     *
     * @return mixed
     */
    public function getDelegate();
}
