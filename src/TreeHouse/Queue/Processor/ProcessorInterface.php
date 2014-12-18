<?php

namespace TreeHouse\Queue\Processor;

use TreeHouse\Queue\Message\Message;

interface ProcessorInterface
{
    /**
     * Processes a message and returns a boolean value. The result should incidate whether the message could be
     * processed, regardless of the outcome. Only in case of an error, after which you want to process the message again
     * later, should you return false here. In all other cases this should return true.
     *
     * @param Message $message
     *
     * @return boolean False if the message could not be processed, true otherwise
     */
    public function process(Message $message);
}
