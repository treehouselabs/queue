<?php

namespace TreeHouse\Queue\Message\Composer;

use TreeHouse\Queue\Message\Message;
use TreeHouse\Queue\Message\MessageProperties;
use TreeHouse\Queue\Message\Serializer\SerializerInterface;

class DefaultMessageComposer implements MessageComposerInterface
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @inheritdoc
     */
    public function compose($payload, array $properties = [], $id = null, $routingKey = null)
    {
        $defaults = [
            'content_type' => MessageProperties::CONTENT_TYPE_TEXT_PLAIN,
            'delivery_mode' => MessageProperties::DELIVERY_MODE_PERSISTENT,
        ];

        $properties = new MessageProperties(array_merge($defaults, $properties));

        return new Message($this->serialize($payload), $properties, $id, $routingKey);
    }

    /**
     * @param mixed $payload
     *
     * @return string
     */
    protected function serialize($payload)
    {
        return $this->serializer->serialize($payload);
    }
}
