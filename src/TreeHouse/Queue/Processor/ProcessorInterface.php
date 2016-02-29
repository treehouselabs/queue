<?php

namespace TreeHouse\Queue\Processor;

use TreeHouse\Queue\Amqp\EnvelopeInterface;

interface ProcessorInterface
{
    /**
     * Processes a message and returns the result.
     *
     * Any exceptions thrown here will nack the message and - according to your
     * setup - retry, dead-letter, or discard them.
     *
     * @param EnvelopeInterface $envelope
     *
     * @return mixed The processing result
     */
    public function process(EnvelopeInterface $envelope);
}
