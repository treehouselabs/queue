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
    public function createMessage($payload, $priority = self::DEFAULT_PRIORITY)
    {
        $message = $this->messageComposer->compose($payload);
        $message->setPriority($priority);

        return $message;
    }

    /**
     * @inheritdoc
     */
    public function publish(Message $message, \DateTime $date = null, $flags = AMQP_NOPARAM)
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
//            $flags = AMQP_NOPARAM;
//            $message->setRoutingKey($this->getDeferredQueueName());
//            $message->getProperties()->set('ttl', $seconds);
        }

        $body  = $message->getBody();
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

            $this->deferredQueue = new \AmqpQueue($this->exchange->getChannel());
            $this->deferredQueue->setName($name);
            $this->deferredQueue->setFlags(AMQP_DURABLE);
            $this->deferredQueue->setArguments([
                'x-dead-letter-exchange' => $this->exchange->getName(),
                'x-dead-letter-routing-key' => '',
                'x-message-ttl'          => 600, // 10 minutes by default
            ]);

            $this->deferredQueue->declareQueue();
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
