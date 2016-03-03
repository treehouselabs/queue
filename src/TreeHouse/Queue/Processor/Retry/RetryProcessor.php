<?php

namespace TreeHouse\Queue\Processor\Retry;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use TreeHouse\Queue\Amqp\EnvelopeInterface;
use TreeHouse\Queue\Exception\ProcessExhaustedException;
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
     * @param ProcessorInterface     $processor
     * @param RetryStrategyInterface $strategy
     * @param LoggerInterface        $logger
     */
    public function __construct(ProcessorInterface $processor, RetryStrategyInterface $strategy, LoggerInterface $logger = null)
    {
        $this->processor = $processor;
        $this->strategy = $strategy;
        $this->logger = $logger ?: new NullLogger();
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
    public function process(EnvelopeInterface $envelope)
    {
        try {
            $result = $this->processor->process($envelope);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), ['message' => $envelope->getDeliveryTag()]);
            $this->logger->debug($exception->getTraceAsString());

            $result = $this->retryMessage($envelope, $exception);
        }

        return $result;
    }

    /**
     * @param EnvelopeInterface $envelope
     * @param \Exception        $exception
     *
     * @throws ProcessExhaustedException
     *
     * @return bool
     */
    protected function retryMessage(EnvelopeInterface $envelope, \Exception $exception = null)
    {
        $attempt = $this->getAttemptValue($envelope);
        if ($attempt >= $this->maxAttempts) {
            throw new ProcessExhaustedException(sprintf('Exhausted after failing %d attempt(s)', $attempt));
        }

        $this->logger->debug(sprintf('Requeueing message (%d attempts left)', $this->maxAttempts - $attempt));

        return $this->strategy->retry($envelope, ++$attempt, $exception);
    }

    /**
     * @param EnvelopeInterface $envelope
     *
     * @throws \LogicException
     *
     * @return int
     */
    protected function getAttemptValue(EnvelopeInterface $envelope)
    {
        if (false === $attempt = $envelope->getHeader(self::PROPERTY_KEY)) {
            return 1;
        }

        $attempt = (integer) $attempt;

        if ($attempt < 1) {
            throw new \LogicException(
                sprintf('Attempt can only be a positive number, got "%s"', json_encode($attempt))
            );
        }

        return $attempt;
    }
}
