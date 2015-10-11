<?php

namespace TreeHouse\Queue\Message;

class Message
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $body;

    /**
     * @var MessageProperties
     */
    private $properties;

    /**
     * @var string
     */
    private $routingKey;

    /**
     * @param string                 $id
     * @param string                 $body
     * @param MessageProperties|null $properties
     * @param string|null            $routingKey
     */
    public function __construct($body, MessageProperties $properties = null, $id = null, $routingKey = null)
    {
        $this->id = $id;
        $this->body = $body;
        $this->properties = $properties ?: new MessageProperties();
        $this->routingKey = $routingKey;
    }

    /**
     * Resets id and deep-clones properties.
     */
    public function __clone()
    {
        $this->id = null;
        $this->properties = new MessageProperties($this->properties->toArray());
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param MessageProperties $properties
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;
    }

    /**
     * @return MessageProperties
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param string $routingKey
     */
    public function setRoutingKey($routingKey)
    {
        $this->routingKey = $routingKey;
    }

    /**
     * @return string
     */
    public function getRoutingKey()
    {
        return $this->routingKey;
    }

    /**
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->properties->set(MessageProperties::KEY_CONTENT_TYPE, $contentType);
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->properties->get(MessageProperties::KEY_CONTENT_TYPE);
    }

    /**
     * @param int $deliveryMode
     */
    public function setDeliveryMode($deliveryMode)
    {
        $this->properties->set(MessageProperties::KEY_DELIVERY_MODE, (integer) $deliveryMode);
    }

    /**
     * @return int
     */
    public function getDeliveryMode()
    {
        return $this->properties->get(MessageProperties::KEY_DELIVERY_MODE);
    }

    /**
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->properties->set(MessageProperties::KEY_PRIORITY, (integer) $priority);
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->properties->get(MessageProperties::KEY_PRIORITY);
    }

    /**
     * @param int $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->properties->set(MessageProperties::KEY_TIMESTAMP, (integer) $timestamp);
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->properties->get(MessageProperties::KEY_TIMESTAMP);
    }
}
