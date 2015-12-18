<?php

namespace TreeHouse\Queue\Processor\Retry;

use Psr\Log\LoggerInterface;
use TreeHouse\Queue\Exception\ProcessExhaustedException;
use TreeHouse\Queue\Message\Message;
use TreeHouse\Queue\Processor\ProcessorInterface;

/**
 * Processor that performs a number of attempts when a message could not be processed.
 */
class RetryProcessor implements ProcessorInterface
{
    const PROPERTY_KEY = 'attempt';

    /**
     * @var ProcessorInterface
     */
    protected $processor;

    /**
     * @var RetryStrategyInterface
     */
    protected $strategy;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var int
     */
    protected $maxAttempts = 2;

    /**
     * @var int
     */
    protected $cooldownTime = 600;

    /**
     * @param ProcessorInterface     $processor
     * @param RetryStrategyInterface $strategy
     * @param LoggerInterface        $logger
     */
    public function __construct(ProcessorInterface $processor, RetryStrategyInterface $strategy, LoggerInterface $logger = null)
    {
        $this->processor = $processor;
        $this->strategy = $strategy;
        $this->logger = $logger;
    }

    /**
     * @param ProcessorInterface $processor
     */
    public function setProcessor($processor)
    {
        $this->processor = $processor;
    }

    /**
     * @return ProcessorInterface
     */
    public function getProcessor()
    {
        return $this->processor;
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
     * @param int $maxAttempts
     */
    public function setMaxAttempts($maxAttempts)
    {
        $this->maxAttempts = $maxAttempts;
    }

    /**
     * @return int
     */
    public function getMaxAttempts()
    {
        return $this->maxAttempts;
    }

    /**
     * @inheritdoc
     */
    public function process(Message $message)
    {
        try {
            $result = $this->processor->process($message);
        } catch (\Exception $exception) {
            $result = false;

            if ($this->logger) {
                $this->logger->error($exception->getMessage(), ['message' => $message->getId()]);
            }
        }

        if ($result !== true) {
            $result = $this->retryMessage($message);
        }

        return $result;
    }

    /**
     * @param Message $message
     *
     * @throws ProcessExhaustedException
     *
     * @return bool
     */
    protected function retryMessage(Message $message)
    {
        $attempt = $this->getAttemptValue($message);
        if ($attempt >= $this->maxAttempts) {
            throw new ProcessExhaustedException(sprintf('Exhausted after failing %d attempt(s)', $attempt));
        }

        if ($this->logger) {
            $this->logger->debug(sprintf('Requeueing message (%d attempts left)', $this->maxAttempts - $attempt));
        }

        return $this->strategy->retry($message, ++$attempt);
    }

    /**
     * @param Message $message
     *
     * @throws \LogicException
     *
     * @return int
     */
    protected function getAttemptValue(Message $message)
    {
        $properties = $message->getProperties();
        if (!$properties->has(self::PROPERTY_KEY)) {
            return 1;
        }

        $attempt = (integer) $properties->get(self::PROPERTY_KEY);

        if ($attempt < 1) {
            throw new \LogicException(
                sprintf('Attempt can only be a positive number, got "%s"', json_encode($attempt))
            );
        }

        return $attempt;
    }
}
