<?php

namespace TreeHouse\Queue\Processor;

use TreeHouse\Queue\Message\Message;

interface ProcessorInterface
{
    /**
     * @param Message $message
     */
    public function process(Message $message);
}
