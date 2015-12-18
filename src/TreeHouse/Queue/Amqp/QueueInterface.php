<?php

namespace TreeHouse\Queue\Amqp;

use TreeHouse\Queue\Exception\ChannelException;
use TreeHouse\Queue\Exception\ConnectionException;

interface QueueInterface
{
    const NOPARAM = 0;
    const DURABLE = 1;
    const PASSIVE = 2;
    const EXCLUSIVE = 4;
    const AUTODELETE = 8;
    const MULTIPLE = 16;
    const AUTOACK = 32;
    const REQUEUE = 64;
    const IFUNUSED = 128;
    const IFEMPTY = 256;

    /**
     * @return ChannelInterface
     */
    public function getChannel();

    /**
     * @return ConnectionInterface
     */
    public function getConnection();

    /**
     * Set the queue name.
     *
     * @param string $name The name of the queue.
     *
     * @return boolean
     */
    public function setName($name);

    /**
     * Get the configured name.
     *
     * @return string The configured name as a string.
     */
    public function getName();

    /**
     * Set a queue argument.
     *
     * @param string $key   The key to set.
     * @param mixed  $value The value to set.
     *
     * @return bool
     */
    public function setArgument($key, $value);

    /**
     * Set all arguments on the given queue.
     *
     * All other argument settings will be wiped.
     *
     * @param array $arguments An array of key/value pairs of arguments.
     *
     * @return bool
     */
    public function setArguments(array $arguments);

    /**
     * Check whether a queue has specific argument.
     *
     * @param string $key The key to check.
     *
     * @return bool
     */
    public function hasArgument($key);

    /**
     * Get the argument associated with the given key.
     *
     * @param string $key The key to look up.
     *
     * @return mixed|false The value associated with the given key, or false if
     *                     the key is not set.
     */
    public function getArgument($key);

    /**
     * Get all set arguments as an array of key/value pairs.
     *
     * @return array An array containing all of the set key/value pairs.
     */
    public function getArguments();

    /**
     * Set the flags on the queue.
     *
     * @param integer $flags A bitmask of flags: durable, passive, exclusive, autodelete
     *
     * @return boolean
     */
    public function setFlags($flags);

    /**
     * Get all the flags currently set on the given queue.
     *
     * @return int An integer bitmask of all the flags currently set on this
     *             exchange object.
     */
    public function getFlags();

    /**
     * Get latest consumer tag. If no consumer available or the latest on was
     * canceled null will be returned.
     *
     * @return string|null
     */
    public function getConsumerTag();

    /**
     * Declare a new queue on the broker.
     *
     * @throws ChannelException    If the channel is not open.
     * @throws ConnectionException If the connection to the broker was lost.
     *
     * @return int the message count.
     */
    public function declareQueue();

    /**
     * Bind the given queue to a routing key on an exchange.
     *
     * @param string $exchangeName Name of the exchange to bind to.
     * @param string $routingKey   Pattern or routing key to bind with.
     * @param array  $arguments    Additional binding arguments.
     *
     * @throws ChannelException    If the channel is not open.
     * @throws ConnectionException If the connection to the broker was lost.
     *
     * @return bool
     */
    public function bind($exchangeName, $routingKey, array $arguments = []);

    /**
     * Remove a routing key binding on an exchange from the given queue.
     *
     * @param string $exchangeName The name of the exchange on which the queue is bound.
     * @param string $routingKey   The binding routing key used by the queue.
     * @param array  $arguments    Additional binding arguments.
     *
     * @throws ChannelException    If the channel is not open.
     * @throws ConnectionException If the connection to the broker was lost.
     *
     * @return bool
     */
    public function unbind($exchangeName, $routingKey = null, array $arguments = []);

    /**
     * Retrieve the next message from the queue.
     *
     * Retrieve the next available message from the queue. If no messages are present in the queue, this function will
     * return `false` immediately. This is a non blocking alternative to the `consume()` method.
     *
     * Currently, the only supported flag for the flags parameter is AUTOACK. If this flag is passed in, then the
     * message returned will automatically be marked as acknowledged by the broker as soon as the frames are sent to the
     * client.
     *
     * @param int $flags A bitmask of supported flags for the method call. Currently, the only the supported flag is
     *                   `AUTOACK`. If this value is not provided, it will use the value of ini-setting
     *                   `amqp.auto_ack`.
     *
     * @throws ChannelException    If the channel is not open.
     * @throws ConnectionException If the connection to the broker was lost.
     *
     * @return EnvelopeInterface|bool
     */
    public function get($flags = null);

    /**
     * Consume messages from a queue.
     *
     * Blocking function that will retrieve the next message from the queue as it becomes available and will pass it off
     * to the callback.
     *
     * @param callable $callback    A callback function to which the consumed message will be passed. The function must
     *                              accept at a minimum one parameter, an envelope object, and an optional second
     *                              parameter: the queue from which the message was consumed. The method will not return
     *                              the processing thread back to the PHP script until the callback function returns
     *                              `false`
     * @param int      $flags       A bitmask of any of the flags: AUTOACK.
     * @param string   $consumerTag A string describing this consumer. Used for canceling subscriptions with `cancel()`.
     *
     * @throws ChannelException    If the channel is not open.
     * @throws ConnectionException If the connection to the broker was lost.
     */
    public function consume(callable $callback, $flags = null, $consumerTag = null);

    /**
     * Acknowledge the receipt of a message.
     *
     * @param string $deliveryTag The message delivery tag of which to acknowledge receipt.
     * @param int    $flags       The only valid flag that can be passed is MULTIPLE.
     *
     * @throws ChannelException    If the channel is not open.
     * @throws ConnectionException If the connection to the broker was lost.
     *
     * @return bool
     */
    public function ack($deliveryTag, $flags = null);

    /**
     * Mark a message as explicitly not acknowledged.
     *
     * Mark the message identified by delivery_tag as explicitly not acknowledged. This method can only be called on
     * messages that have not yet been acknowledged, meaning that messages retrieved with by `consume()` and `get()`
     * and using the AUTOACK flag are not eligible. When called, the broker will immediately put the message back onto
     * the queue, instead of waiting until the connection is closed. This method is only supported by the RabbitMQ
     * broker. The behavior of calling this method while connected to any other broker is undefined.
     *
     * @param string $deliveryTag Delivery tag of last message to reject.
     * @param int    $flags       REQUEUE to requeue the message(s), MULTIPLE to nack all previous unacked messages as well.
     *
     * @throws ChannelException    If the channel is not open.
     * @throws ConnectionException If the connection to the broker was lost.
     *
     * @return bool
     */
    public function nack($deliveryTag, $flags = null);

    /**
     * Mark one message as explicitly not acknowledged.
     *
     * Mark the message identified by delivery_tag as explicitly not acknowledged. This method can only be called on
     * messages that have not yet been acknowledged, meaning that messages retrieved with by `consume()` and `get()` and
     * using the AUTOACK flag are not eligible.
     *
     * @param string $deliveryTag Delivery tag of the message to reject.
     * @param int    $flags       REQUEUE to requeue the message(s).
     *
     * @throws ChannelException    If the channel is not open.
     * @throws ConnectionException If the connection to the broker was lost.
     *
     * @return bool
     */
    public function reject($deliveryTag, $flags = null);

    /**
     * Cancel a queue that is already bound to an exchange and routing key.
     *
     * @param string $consumerTag The queue name to cancel, if the queue object is not already representative of a queue.
     *
     * @throws ChannelException    If the channel is not open.
     * @throws ConnectionException If the connection to the broker was lost.
     *
     * @return bool
     */
    public function cancel($consumerTag = '');

    /**
     * Delete a queue from the broker.
     *
     * This includes its entire contents of unread or unacknowledged messages.
     *
     * @param int $flags Optionally IFUNUSED can be specified to indicate the queue should not be deleted until no
     *                   clients are connected to it.
     *
     * @throws ChannelException    If the channel is not open.
     * @throws ConnectionException If the connection to the broker was lost.
     *
     * @return int The number of deleted messages.
     */
    public function delete($flags = null);

    /**
     * Purge the contents of a queue.
     *
     * @throws ChannelException    If the channel is not open.
     * @throws ConnectionException If the connection to the broker was lost.
     *
     * @return bool
     */
    public function purge();

    /**
     * When using a decorator, you can use this to get the decorated object.
     *
     * @return mixed
     */
    public function getDelegate();
}
