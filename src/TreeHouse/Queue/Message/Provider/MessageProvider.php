<?php

namespace TreeHouse\Queue\Message\Provider;

use TreeHouse\Queue\Amqp\EnvelopeInterface;
use TreeHouse\Queue\Amqp\QueueInterface;
use TreeHouse\Queue\Message\Message;
use TreeHouse\Queue\Message\MessageProperties;

class MessageProvider implements MessageProviderInterface
{
    /**
     * @var QueueInterface
     */
    protected $queue;

    /**
     * @param QueueInterface $queue
     */
    public function __construct(QueueInterface $queue)
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

        $id = $envelope->getDeliveryTag();
        $body = $envelope->getBody();
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
        $this->queue->nack($message->getId(), $requeue ? QueueInterface::REQUEUE : null);
    }

    /**
     * @inheritdoc
     */
    public function consume(callable $callback)
    {
        $this->queue->consume(function(EnvelopeInterface $envelope) use ($callback) {
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
