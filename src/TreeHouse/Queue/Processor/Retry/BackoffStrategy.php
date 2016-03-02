<?php

namespace TreeHouse\Queue\Processor\Retry;

use TreeHouse\Queue\Amqp\EnvelopeInterface;
use TreeHouse\Queue\Message\Message;
use TreeHouse\Queue\Message\Publisher\MessagePublisherInterface;

/**
 * Retries a message, but with an increasing delay for every attempt.
 */
class BackoffStrategy extends AbstractStrategy
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
    public function retry(EnvelopeInterface $envelope, $attempt, \Exception $exception = null)
    {
        $message = $this->createRetryMessage($envelope, $attempt, $exception);

        // multiply cooldown time by the attempt number,
        $cooldownTime = $attempt * $this->cooldownTime;
        $cooldownDate = \DateTime::createFromFormat('U', (time() + $cooldownTime));

        return $this->publisher->publish($message, $cooldownDate);
    }
}
