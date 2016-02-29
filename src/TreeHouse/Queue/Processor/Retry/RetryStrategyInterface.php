<?php

namespace TreeHouse\Queue\Processor\Retry;

use TreeHouse\Queue\Amqp\EnvelopeInterface;

interface RetryStrategyInterface
{
    /**
     * @param EnvelopeInterface $envelope The message to retry
     * @param int               $attempt  The next attempt value
     * @param \Exception        $exception
     *
     * @return bool The result to pass to the process function
     */
    public function retry(EnvelopeInterface $envelope, $attempt, \Exception $exception = null);
}
