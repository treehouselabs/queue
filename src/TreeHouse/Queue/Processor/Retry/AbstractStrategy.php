<?php

namespace TreeHouse\Queue\Processor\Retry;

use TreeHouse\Queue\Amqp\EnvelopeInterface;
use TreeHouse\Queue\Message\Message;
use TreeHouse\Queue\Message\MessageProperties;

abstract class AbstractStrategy implements RetryStrategyInterface
{
    /**
     * Creates a new message to retry.
     *
     * @param EnvelopeInterface $envelope
     * @param int               $attempt
     * @param \Exception        $exception
     *
     * @return Message
     */
    protected function createRetryMessage(EnvelopeInterface $envelope, $attempt, \Exception $exception = null)
    {
        $headers = $envelope->getHeaders();
        $headers[RetryProcessor::PROPERTY_KEY] = $attempt;

        if ($exception) {
            $headers['x-exception'] = [
                'message' => $exception->getMessage(),
                'type' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ];
        }

        $properties = new MessageProperties([
            MessageProperties::KEY_CONTENT_TYPE => $envelope->getContentType(),
            MessageProperties::KEY_DELIVERY_MODE => $envelope->getDeliveryMode(),
            MessageProperties::KEY_HEADERS => $headers,
            MessageProperties::KEY_PRIORITY => $envelope->getPriority(),
        ]);

        return new Message(
            $envelope->getBody(),
            $properties,
            $envelope->getDeliveryTag(),
            $envelope->getRoutingKey()
        );
    }
}
