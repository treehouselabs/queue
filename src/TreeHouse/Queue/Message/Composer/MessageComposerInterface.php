<?php

namespace TreeHouse\Queue\Message\Composer;

use TreeHouse\Queue\Message\Message;

interface MessageComposerInterface
{
    /**
     * @param mixed       $payload
     * @param array|null  $properties
     * @param string|null $id
     * @param string|null $routingKey
     *
     * @return Message
     */
    public function compose($payload, array $properties = null, $id = null, $routingKey = null);
}
