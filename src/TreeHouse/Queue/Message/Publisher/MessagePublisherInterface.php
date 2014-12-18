<?php

namespace TreeHouse\Queue\Message\Publisher;

use TreeHouse\Queue\Message\Message;

interface MessagePublisherInterface
{
    const DEFAULT_PRIORITY = 0;

    /**
     * @param mixed   $payload  The message payload
     * @param integer $priority Priority for the message, this must be a number between 0 (lowest) and 9 (highest)
     *
     * @return Message
     */
    public function createMessage($payload, $priority = self::DEFAULT_PRIORITY);

    /**
     * @param Message   $message The message
     * @param \DateTime $date    The date to publish the message (not yet implemented!)
     * @param integer   $flags   AMQP flags to publish the message with
     *
     * @return bool True on success, false on failure
     */
    public function publish(Message $message, \DateTime $date = null, $flags = AMQP_NOPARAM);
}
