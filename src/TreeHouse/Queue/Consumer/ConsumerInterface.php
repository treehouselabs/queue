<?php

namespace TreeHouse\Queue\Consumer;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use TreeHouse\Queue\Amqp\EnvelopeInterface;
use TreeHouse\Queue\Amqp\QueueInterface;

interface ConsumerInterface
{
    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher();

    /**
     * Flag whether to requeue messages when NACK-ing them.
     * Set this to false if you've set up a dead letter exchange.
     *
     * @param bool $requeue
     */
    public function setNackRequeue($requeue);

    /**
     * @return EnvelopeInterface
     */
    public function get();

    /**
     * @param EnvelopeInterface $envelope The message to ACK
     */
    public function ack(EnvelopeInterface $envelope);

    /**
     * @param EnvelopeInterface $envelope The message to NACK
     * @param bool              $requeue  Whether to requeue the message in the queue
     */
    public function nack(EnvelopeInterface $envelope, $requeue = false);

    /**
     * The callback to consume messages with. Receives an instance of EnvelopeInterface.
     *
     * @param callable $callback
     */
    public function setCallback(callable $callback);

    /**
     * Consumes messages.
     *
     * @param int $flags A bitmask of any of the flags: QueueInterface::AUTOACK.
     */
    public function consume($flags = QueueInterface::NOPARAM);
}
