<?php

namespace TreeHouse\Queue\Message\Provider;

use TreeHouse\Queue\Message\Message;

interface MessageProviderInterface
{
    /**
     * @return Message
     */
    public function get();

    /**
     * Blocking function to consume next message
     *
     * @param callable $callback called when a message is available
     *
     * @return void
     */
    public function consume(callable $callback);

    /**
     * @param Message $message The message to ACK
     */
    public function ack(Message $message);

    /**
     * @param Message $message The message to NACK
     * @param bool    $requeue Requeue the message in the queue?
     */
    public function nack(Message $message, $requeue = false);
}
