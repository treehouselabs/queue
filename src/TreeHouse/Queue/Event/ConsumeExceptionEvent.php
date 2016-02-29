<?php

namespace TreeHouse\Queue\Event;

use TreeHouse\Queue\Amqp\EnvelopeInterface;

class ConsumeExceptionEvent extends ConsumeEvent
{
    /**
     * @var \Exception
     */
    private $exception;

    /**
     * @param EnvelopeInterface $envelope
     * @param \Exception        $exception
     */
    public function __construct(EnvelopeInterface $envelope, \Exception $exception)
    {
        parent::__construct($envelope);

        $this->exception = $exception;
    }

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }
}
