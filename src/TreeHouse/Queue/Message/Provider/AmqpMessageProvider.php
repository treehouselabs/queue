<?php

namespace TreeHouse\Queue\Message\Provider;

use TreeHouse\Queue\Message\Message;
use TreeHouse\Queue\Message\MessageProperties;

class AmqpMessageProvider implements MessageProviderInterface
{
    /**
     * @var \AMQPQueue
     */
    protected $queue;

    /**
     * @param \AMQPQueue $queue
     */
    public function __construct(\AMQPQueue $queue)
    {
        $this->queue = $queue;
    }

    /**
     * @inheritdoc
     */
    public function get()
    {
        if (false === $envelope = $this->queue->get()) {
            return null;
        }

        $id    = $envelope->getDeliveryTag();
        $body  = $envelope->getBody();
        $props = new MessageProperties($envelope->getHeaders());

        return new Message($body, $props, $id);
    }

    /**
     * @inheritdoc
     */
    public function ack(Message $message)
    {
        $this->queue->ack($message->getId());
    }

    /**
     * @inheritdoc
     */
    public function nack(Message $message, $requeue = false)
    {
        $this->queue->nack($message->getId(), $requeue ? AMQP_REQUEUE : null);
    }

    /**
     * @inheritdoc
     */
    public function consume(callable $callback)
    {
        $this->queue->consume(function(\AMQPEnvelope $envelope) use ($callback) {
            $id    = $envelope->getDeliveryTag();
            $body  = $envelope->getBody();
            $props = new MessageProperties($envelope->getHeaders());

            $message = new Message($body, $props, $id);

            $callback($message);
            
            // release blocking thread back to php
            return false;
        });
    }
}
