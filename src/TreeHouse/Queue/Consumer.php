<?php

namespace TreeHouse\Queue;

use TreeHouse\Queue\Message\Provider\MessageProviderInterface;
use TreeHouse\Queue\Processor\ProcessorInterface;

class Consumer
{
    /**
     * @var MessageProviderInterface
     */
    protected $messageProvider;

    /**
     * @var ProcessorInterface
     */
    protected $processor;

    /**
     * Constructor
     *
     * @param MessageProviderInterface $messageProvider
     * @param ProcessorInterface       $processor
     */
    public function __construct(MessageProviderInterface $messageProvider, ProcessorInterface $processor)
    {
        $this->messageProvider = $messageProvider;
        $this->processor       = $processor;
    }

    /**
     * @param MessageProviderInterface $messageProvider
     */
    public function setMessageProvider($messageProvider)
    {
        $this->messageProvider = $messageProvider;
    }

    /**
     * @return MessageProviderInterface
     */
    public function getMessageProvider()
    {
        return $this->messageProvider;
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
     * Consumes messages
     */
    public function consume()
    {
        while (null !== $message = $this->messageProvider->get()) {
            if ($this->processor->process($message)) {
                $this->messageProvider->ack($message);
            }
        }
    }
}
