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
     * @var bool
     */
    private $continue;

    /**
     * @param EnvelopeInterface $envelope The envelope that was consumed
     * @param mixed             $result   The result of the consumed message
     * @param bool              $continue  Whether to continue consuming or stop the thread blocking
     */
    public function __construct(EnvelopeInterface $envelope, $result = null, $continue = true)
    {
        $this->envelope = $envelope;
        $this->result = $result;
        $this->continue = $continue;
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

    /**
     * @return bool
     */
    public function continue()
    {
        return $this->continue;
    }

    /**
     * Marks the event as to not continue consuming after this.
     */
    public function stopConsuming()
    {
        $this->continue = false;
    }
}
