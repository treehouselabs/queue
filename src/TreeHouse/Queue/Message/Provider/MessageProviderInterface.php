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
     * @param Message $message The message to ACK
     *
     * @return void
     */
    public function ack(Message $message);

    /**
     * @param Message $message The message to NACK
     * @param boolean $requeue Requeue the message in the queue?
     *
     * @return void
     */
    public function nack(Message $message, $requeue = false);
}
