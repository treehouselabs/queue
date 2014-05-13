<?php

namespace TreeHouse\Queue\Message;

class Message
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var MessageProperties
     */
    protected $properties;

    /**
     * @var string
     */
    protected $routingKey;

    /**
     * @param string                 $id
     * @param string                 $body
     * @param MessageProperties|null $properties
     * @param string|null            $routingKey
     */
    public function __construct($body, MessageProperties $properties = null, $id = null, $routingKey = null)
    {
        if (null === $properties) {
            $properties = new MessageProperties();
        }

        $this->id         = $id;
        $this->body       = $body;
        $this->properties = $properties;
        $this->routingKey = $routingKey;
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
}
