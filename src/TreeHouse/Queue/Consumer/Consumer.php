<?php

namespace TreeHouse\Queue\Consumer;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use TreeHouse\Queue\Amqp\EnvelopeInterface;
use TreeHouse\Queue\Amqp\QueueInterface;
use TreeHouse\Queue\Event\ConsumeEvent;
use TreeHouse\Queue\Event\ConsumeExceptionEvent;
use TreeHouse\Queue\Processor\ProcessorInterface;
use TreeHouse\Queue\QueueEvents;

class Consumer implements ConsumerInterface
{
    /**
     * @var QueueInterface
     */
    protected $queue;

    /**
     * @var ProcessorInterface
     */
    protected $processor;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var callable
     */
    protected $callback;

    /**
     * @var bool
     */
    protected $nackRequeue = false;

    /**
     * @param QueueInterface           $queue
     * @param ProcessorInterface       $processor
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(QueueInterface $queue, ProcessorInterface $processor, EventDispatcherInterface $dispatcher = null)
    {
        $this->queue = $queue;
        $this->processor = $processor;
        $this->dispatcher = $dispatcher ?: new EventDispatcher();

        $this->setCallback(
            function (EnvelopeInterface $envelope) {
                try {
                    $event = new ConsumeEvent($envelope);
                    $this->dispatcher->dispatch(QueueEvents::CONSUME_MESSAGE, $event);

                    $result = $this->processor->process($envelope);

                    $event->setResult($result);
                    $this->dispatcher->dispatch(QueueEvents::CONSUMED_MESSAGE, $event);

                    $this->ack($envelope);

                    return $result;
                } catch (\Exception $exception) {
                    $this->dispatcher->dispatch(QueueEvents::CONSUME_EXCEPTION, new ConsumeExceptionEvent($envelope, $exception));

                    $this->nack($envelope, $this->nackRequeue);

                    throw $exception;
                }
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function getEventDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @inheritdoc
     */
    public function setNackRequeue($requeue)
    {
        $this->nackRequeue = $requeue;
    }

    /**
     * @param callable $callback
     */
    public function setCallback(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @inheritdoc
     */
    public function get()
    {
        if (false === $envelope = $this->queue->get()) {
            return null;
        }

        return $envelope;
    }

    /**
     * @inheritdoc
     */
    public function ack(EnvelopeInterface $envelope)
    {
        $this->queue->ack($envelope->getDeliveryTag());
    }

    /**
     * @inheritdoc
     */
    public function nack(EnvelopeInterface $envelope, $requeue = false)
    {
        $this->queue->nack($envelope->getDeliveryTag(), $requeue ? QueueInterface::REQUEUE : null);
    }

    /**
     * @inheritdoc
     */
    public function consume($flags = QueueInterface::NOPARAM)
    {
        $this->queue->consume($this->callback, $flags);
    }
}
