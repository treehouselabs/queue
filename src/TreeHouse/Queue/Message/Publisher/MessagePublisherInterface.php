<?php

namespace TreeHouse\Queue\Message\Publisher;

use TreeHouse\Queue\Message\Message;

interface MessagePublisherInterface
{
    /**
     * @param mixed $payload
     *
     * @return Message
     */
    public function createMessage($payload);

    /**
     * @param Message   $message
     * @param boolean   $priority
     * @param \DateTime $date
     *
     * @return boolean True on success, false on failure
     */
    public function publish(Message $message, $priority = false, \DateTime $date = null);
}
