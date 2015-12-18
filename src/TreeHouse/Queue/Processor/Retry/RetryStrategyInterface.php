<?php

namespace TreeHouse\Queue\Processor\Retry;

use TreeHouse\Queue\Message\Message;

interface RetryStrategyInterface
{
    /**
     * @param Message $message The message to retry
     * @param int     $attempt The next attempt value
     *
     * @return bool The result to pass to the process function
     */
    public function retry(Message $message, $attempt);
}
