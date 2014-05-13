<?php

namespace TreeHouse\Queue\Message\Publisher;

use TreeHouse\Queue\Message\Message;
use TreeHouse\Queue\Message\Composer\MessageComposerInterface;

class AmqpMessagePublisher implements MessagePublisherInterface
{
    /**
     * @var \AmqpExchange
     */
    protected $exchange;

    /**
     * @var MessageComposerInterface
     */
    protected $messageComposer;

    /**
     * Automatically created queue for deferred messages
     *
     * @var \AmqpQueue
     */
    protected $deferredQueue;

    /**
     * @param \AMQPExchange            $exchange
     * @param MessageComposerInterface $messageComposer
     */
    public function __construct(\AMQPExchange $exchange, MessageComposerInterface $messageComposer)
    {
        $this->exchange        = $exchange;
        $this->messageComposer = $messageComposer;
    }

    /**
     * @inheritdoc
     */
    public function createMessage($payload)
    {
        return $this->messageComposer->compose($payload);
    }

    /**
     * @inheritdoc
     */
    public function publish(Message $message, $priority = false, \DateTime $date = null)
    {
        $body       = $message->getBody();
        $route      = $message->getRoutingKey();
        $flags      = (true === $priority) ? AMQP_IMMEDIATE : AMQP_NOPARAM;
        $properties = $message->getProperties()->toArray();

        if ($date instanceof \DateTime) {
            if (true === $priority) {
                throw new \LogicException('You cannot set a publish date for a high priority message');
            }

            if ($date < new \DateTime()) {
                throw new \LogicException('You cannot publish a message in the past');
            }

            $route = $this->getDeferredQueue()->getName();
            $flags = AMQP_NOPARAM;
            $properties['ttl'] = $date->getTimestamp() - time();
        }

        return $this->exchange->publish($body, $route, $flags, $properties);
    }

    /**
     * @return \AmqpQueue
     */
    protected function getDeferredQueue()
    {
        if (null === $this->deferredQueue) {
            $name = sprintf('%s.deferred', $this->exchange->getName());

            $this->deferredQueue = new \AmqpQueue($this->exchange->getChannel());
            $this->deferredQueue->setName($name);
            $this->deferredQueue->setFlags(AMQP_DURABLE);
            $this->deferredQueue->setArguments([
                'x-dead-letter-exchange' => $this->exchange->getName(),
                'x-message-ttl'          => 600, // 10 minutes by default
            ]);

            $this->deferredQueue->declareQueue();
            $this->deferredQueue->bind($this->exchange->getName(), $name);
        }

        return $this->deferredQueue;
    }
}
