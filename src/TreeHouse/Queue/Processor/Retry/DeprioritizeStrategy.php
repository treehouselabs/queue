<?php

namespace TreeHouse\Queue\Processor\Retry;

use TreeHouse\Queue\Amqp\EnvelopeInterface;
use TreeHouse\Queue\Message\Publisher\MessagePublisherInterface;

/**
 * Retries the message, but with the priority decreasing with every attempt.
 */
class DeprioritizeStrategy extends AbstractStrategy
{
    /**
     * @var MessagePublisherInterface
     */
    protected $publisher;

    /**
     * @param MessagePublisherInterface $publisher
     */
    public function __construct(MessagePublisherInterface $publisher)
    {
        $this->publisher = $publisher;
    }

    /**
     * @inheritdoc
     */
    public function retry(EnvelopeInterface $envelope, $attempt, \Exception $exception = null)
    {
        // decrease priority with every attempt
        $priority = $envelope->getPriority();
        if ($priority > 0) {
            --$priority;
        }

        $message = $this->createRetryMessage($envelope, $attempt, $exception);
        $message->setPriority($priority);

        return $this->publisher->publish($message);
    }
}
