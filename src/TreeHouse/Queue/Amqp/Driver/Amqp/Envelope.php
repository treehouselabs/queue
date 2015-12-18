<?php

namespace TreeHouse\Queue\Amqp\Driver\Amqp;

use TreeHouse\Queue\Amqp\EnvelopeInterface;

class Envelope implements EnvelopeInterface
{
    /**
     * @var \AMQPEnvelope
     */
    protected $delegate;

    /**
     * @param \AMQPEnvelope $envelope
     */
    public function __construct(\AMQPEnvelope $envelope)
    {
        $this->delegate = $envelope;
    }

    /**
     * @inheritdoc
     */
    public function getAppId()
    {
        return $this->delegate->getAppId();
    }

    /**
     * @inheritdoc
     */
    public function getBody()
    {
        return $this->delegate->getBody();
    }

    /**
     * @inheritdoc
     */
    public function getContentEncoding()
    {
        return $this->delegate->getContentEncoding();
    }

    /**
     * @inheritdoc
     */
    public function getContentType()
    {
        return $this->delegate->getContentType();
    }

    /**
     * @inheritdoc
     */
    public function getCorrelationId()
    {
        return $this->delegate->getCorrelationId();
    }

    /**
     * @inheritdoc
     */
    public function getDeliveryMode()
    {
        return $this->delegate->getDeliveryMode();
    }

    /**
     * @inheritdoc
     */
    public function getDeliveryTag()
    {
        return $this->delegate->getDeliveryTag();
    }

    /**
     * @inheritdoc
     */
    public function getExchangeName()
    {
        return $this->delegate->getExchangeName();
    }

    /**
     * @inheritdoc
     */
    public function getExpiration()
    {
        return $this->delegate->getExpiration();
    }

    /**
     * @inheritdoc
     */
    public function getHeader($headerKey)
    {
        return $this->delegate->getHeader($headerKey);
    }

    /**
     * @inheritdoc
     */
    public function getHeaders()
    {
        return $this->delegate->getHeaders();
    }

    /**
     * @inheritdoc
     */
    public function getMessageId()
    {
        return $this->delegate->getMessageId();
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return $this->delegate->getPriority();
    }

    /**
     * @inheritdoc
     */
    public function getReplyTo()
    {
        return $this->delegate->getReplyTo();
    }

    /**
     * @inheritdoc
     */
    public function getRoutingKey()
    {
        return $this->delegate->getRoutingKey();
    }

    /**
     * @inheritdoc
     */
    public function getTimeStamp()
    {
        return $this->delegate->getTimeStamp();
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->delegate->getType();
    }

    /**
     * @inheritdoc
     */
    public function getUserId()
    {
        return $this->delegate->getUserId();
    }

    /**
     * @inheritdoc
     */
    public function isRedelivery()
    {
        return $this->delegate->isRedelivery();
    }
}
