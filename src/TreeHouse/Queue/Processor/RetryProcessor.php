<?php

namespace TreeHouse\Queue\Processor;

use Psr\Log\LoggerInterface;
use TreeHouse\Queue\Exception\ProcessExhaustedException;
use TreeHouse\Queue\Message\Message;
use TreeHouse\Queue\Message\Provider\MessageProviderInterface;

class RetryProcessor implements ProcessorInterface
{
    const PROPERTY_KEY = 'attempts';

    /**
     * @var ProcessorInterface
     */
    protected $processor;

    /**
     * @var MessageProviderInterface
     */
    protected $provider;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var integer
     */
    protected $maxAttempts = 2;

    /**
     * @var integer
     */
    protected $cooldownTime = 600;

    /**
     * @param ProcessorInterface       $processor
     * @param MessageProviderInterface $provider
     * @param LoggerInterface          $logger
     */
    public function __construct(ProcessorInterface $processor, MessageProviderInterface $provider, LoggerInterface $logger = null)
    {
        $this->processor = $processor;
        $this->provider  = $provider;
        $this->logger    = $logger;
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
     * @param integer $cooldownTime
     */
    public function setCooldownTime($cooldownTime)
    {
        $this->cooldownTime = $cooldownTime;
    }

    /**
     * @return integer
     */
    public function getCooldownTime()
    {
        return $this->cooldownTime;
    }

    /**
     * @param integer $maxAttempts
     */
    public function setMaxAttempts($maxAttempts)
    {
        $this->maxAttempts = $maxAttempts;
    }

    /**
     * @return integer
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
                $this->logger->warning($exception->getMessage(), ['message' => $message->getId()]);
            }
        }

        if ($result !== true) {
            $this->retryMessage($message);
        }

        return $result;
    }

    /**
     * @param Message    $message
     *
     * @throws ProcessExhaustedException
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

        $message->getProperties()->set(self::PROPERTY_KEY, ++$attempt);

        // TODO: find a way to implement the cooldown period
        // $date = new \DateTime('@' . (time() + $this->cooldownTime));

        $this->provider->nack($message, true);
    }

    /**
     * @param Message $message
     *
     * @throws \LogicException
     *
     * @return integer
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
