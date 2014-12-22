<?php

namespace TreeHouse\Queue\Processor\Retry;

use TreeHouse\Queue\Message\Message;

interface RetryStrategyInterface
{
    /**
     * @param Message $message The message to retry
     * @param integer $attempt The next attempt value
     *
     * @return boolean The result to pass to the process function
     */
    public function retry(Message $message, $attempt);
}
