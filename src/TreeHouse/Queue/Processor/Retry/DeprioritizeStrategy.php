<?php

namespace TreeHouse\Queue\Processor\Retry;

use TreeHouse\Queue\Message\Message;
use TreeHouse\Queue\Message\Publisher\MessagePublisherInterface;

/**
 * Retries the message, but with the priority decreasing with every attempt
 */
class DeprioritizeStrategy implements RetryStrategyInterface
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
    public function retry(Message $message, $attempt)
    {
        $message = $this->createRetryMessage($message, $attempt);

        return $this->publisher->publish($message);
    }

    /**
     * Creates a new message to retry
     *
     * @param Message $message
     * @param integer $attempt
     *
     * @return Message
     */
    protected function createRetryMessage(Message $message, $attempt)
    {
        // decrease priority with every attempt
        $priority = $message->getPriority();
        if ($priority > 0) {
            $priority--;
        }

        $newMessage = clone $message;
        $newMessage->setPriority($priority);
        $newMessage->getProperties()->set(RetryProcessor::PROPERTY_KEY, $attempt);

        return $newMessage;
    }
}
