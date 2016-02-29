<?php

namespace TreeHouse\Queue\Amqp\Driver\Amqp\Publisher;

use TreeHouse\Queue\Amqp\ExchangeInterface;
use TreeHouse\Queue\Message\Composer\MessageComposerInterface;
use TreeHouse\Queue\Message\Message;
use TreeHouse\Queue\Message\MessageProperties;
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
            $delay = $date->getTimestamp() - time();
            if ($delay < 0) {
                throw new \OutOFBoundsException('You cannot publish a message in the past');
            }

            // set delay in milliseconds
            $message->setHeader(MessageProperties::KEY_DELAY, $delay * 1000);
        }

        $body = $message->getBody();
        $route = $message->getRoutingKey();
        $props = $message->getProperties()->toArray();

        return $this->exchange->publish($body, $route, $flags, $props);
    }
}
