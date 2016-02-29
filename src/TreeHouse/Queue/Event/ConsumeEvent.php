<?php

namespace TreeHouse\Queue\Event;

use Symfony\Component\EventDispatcher\Event;
use TreeHouse\Queue\Amqp\EnvelopeInterface;

class ConsumeEvent extends Event
{
    /**
     * @var EnvelopeInterface
     */
    private $envelope;

    /**
     * @var mixed
     */
    private $result;

    /**
     * @param EnvelopeInterface $envelope
     * @param mixed             $result
     */
    public function __construct(EnvelopeInterface $envelope, $result = null)
    {
        $this->envelope = $envelope;
        $this->result = $result;
    }

    /**
     * @return EnvelopeInterface
     */
    public function getEnvelope()
    {
        return $this->envelope;
    }

    /**
     * @param mixed $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }
}
