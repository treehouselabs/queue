<?php

namespace TreeHouse\Queue\Amqp\Driver\Amqp\Publisher;

use TreeHouse\Queue\Amqp\Driver\Amqp\AmqpFactory;
use TreeHouse\Queue\Amqp\ExchangeInterface;
use TreeHouse\Queue\Amqp\QueueInterface;
use TreeHouse\Queue\Message\Composer\MessageComposerInterface;
use TreeHouse\Queue\Message\Message;
use TreeHouse\Queue\Message\Publisher\MessagePublisherInterface;

class AmqpMessagePublisher implements MessagePublisherInterface
{
    /**
     * @var ExchangeInterface
     */
    protected $exchange;

    /**
     * @var MessageComposerInterface
     */
    protected $composer;

    /**
     * Automatically created queue for deferred messages.
     *
     * @var QueueInterface
     */
    protected $deferredQueue;

    /**
     * @param ExchangeInterface        $exchange
     * @param MessageComposerInterface $composer
     */
    public function __construct(ExchangeInterface $exchange, MessageComposerInterface $composer)
    {
        $this->exchange = $exchange;
        $this->composer = $composer;
    }

    /**
     * @inheritdoc
     */
    public function createMessage($payload, $priority = self::DEFAULT_PRIORITY)
    {
        $message = $this->composer->compose($payload);
        $message->setPriority($priority);

        return $message;
    }

    /**
     * @inheritdoc
     */
    public function publish(Message $message, \DateTime $date = null, $flags = ExchangeInterface::NOPARAM)
    {
        if ($date instanceof \DateTime) {
            $seconds = $date->getTimestamp() - time();

            if ($seconds < 0) {
                throw new \OutOFBoundsException('You cannot publish a message in the past');
            }

            if ($message->getRoutingKey()) {
                // since we're using the routing key for the deferred queue, we cannot use it here
                throw new \LogicException('Publishing delayed messages with a routing key is unsupported at the moment');
            }

            // publish with the routing key set to the deferred queue
//            $flags = ExchangeInterface::NOPARAM;
//            $message->setRoutingKey($this->getDeferredQueueName());
//            $message->getProperties()->set('ttl', $seconds);
        }

        $body = $message->getBody();
        $route = $message->getRoutingKey();
        $props = $message->getProperties()->toArray();

        return $this->exchange->publish($body, $route, $flags, $props);
    }

    /**
     * @return \AmqpQueue
     */
    protected function getDeferredQueue()
    {
        if (null === $this->deferredQueue) {
            $name = $this->getDeferredQueueName();

            $factory = new AmqpFactory();
            $this->deferredQueue = $factory->createQueue(
                $this->exchange->getChannel(),
                $name,
                QueueInterface::DURABLE,
                [
                    'x-dead-letter-exchange' => $this->exchange->getName(),
                    'x-dead-letter-routing-key' => '',
                    'x-message-ttl' => 600, // 10 minutes by default
                ]
            );

            $this->deferredQueue->bind($this->exchange->getName(), $name);
        }

        return $this->deferredQueue;
    }

    /**
     * @return string
     */
    protected function getDeferredQueueName()
    {
        return sprintf('%s.deferred', $this->exchange->getName());
    }
}
