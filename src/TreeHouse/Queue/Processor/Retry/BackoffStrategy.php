<?php

namespace TreeHouse\Queue\Processor\Retry;

use TreeHouse\Queue\Message\Message;
use TreeHouse\Queue\Message\Publisher\MessagePublisherInterface;

/**
 * Retries a message, but with an increasing delay for every attempt.
 */
class BackoffStrategy implements RetryStrategyInterface
{
    /**
     * @var MessagePublisherInterface
     */
    protected $publisher;

    /**
     * @var int
     */
    protected $cooldownTime;

    /**
     * @param MessagePublisherInterface $publisher
     * @param int                       $cooldownTime
     */
    public function __construct(MessagePublisherInterface $publisher, $cooldownTime = 600)
    {
        $this->publisher = $publisher;
        $this->cooldownTime = $cooldownTime;
    }

    /**
     * @param int $cooldownTime
     */
    public function setCooldownTime($cooldownTime)
    {
        $this->cooldownTime = $cooldownTime;
    }

    /**
     * @return int
     */
    public function getCooldownTime()
    {
        return $this->cooldownTime;
    }

    /**
     * @inheritdoc
     */
    public function retry(Message $message, $attempt)
    {
        $message = $this->createRetryMessage($message, $attempt);

        // multiply cooldown time by the attempt number,
        $cooldownTime = $attempt * $this->cooldownTime;
        $cooldownDate = \DateTime::createFromFormat('U', (time() + $cooldownTime));

        return $this->publisher->publish($message, $cooldownDate);
    }

    /**
     * Creates a new message to retry.
     *
     * @param Message $message
     * @param int     $attempt
     *
     * @return Message
     */
    protected function createRetryMessage(Message $message, $attempt)
    {
        $newMessage = clone $message;
        $newMessage->getProperties()->set(RetryProcessor::PROPERTY_KEY, $attempt);

        return $newMessage;
    }
}
