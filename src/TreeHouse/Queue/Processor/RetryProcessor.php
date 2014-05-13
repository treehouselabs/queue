<?php

namespace TreeHouse\Queue\Processor;

use Psr\Log\LoggerInterface;
use TreeHouse\Queue\Exception\ProcessExhaustedException;
use TreeHouse\Queue\Message\Message;
use TreeHouse\Queue\Message\Publisher\MessagePublisherInterface;

class RetryProcessor implements ProcessorInterface
{
    const PROPERTY_KEY = 'attempts';

    /**
     * @var ProcessorInterface
     */
    protected $processor;

    /**
     * @var MessagePublisherInterface
     */
    protected $publisher;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @todo make configurable
     *
     * @var integer
     */
    protected $maxAttempts = 3;

    /**
     * @var integer
     */
    protected $cooldownTime = 600;

    /**
     * @param ProcessorInterface        $processor
     * @param MessagePublisherInterface $publisher
     * @param LoggerInterface           $logger
     */
    public function __construct(ProcessorInterface $processor, MessagePublisherInterface $publisher, LoggerInterface $logger = null)
    {
        $this->processor = $processor;
        $this->publisher = $publisher;
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
            $this->processor->process($message);
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage(), ['message' => $message->getId()]);

            $attempt = $this->getAttemptValue($message);
            if ($attempt >= $this->maxAttempts) {
                throw new ProcessExhaustedException(sprintf('Exhausted after failing %d attempt(s)', $attempt), 0, $e);
            }

            $message->getProperties()->set(self::PROPERTY_KEY, ++$attempt);

            $this->publisher->publish($message, false, new \DateTime('@' . (time() + $this->cooldownTime)));
        }
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
