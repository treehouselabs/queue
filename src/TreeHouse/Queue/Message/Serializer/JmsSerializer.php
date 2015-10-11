<?php

namespace TreeHouse\Queue\Message\Serializer;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface as BaseSerializer;

class JmsSerializer implements SerializerInterface
{
    /**
     * @var BaseSerializer
     */
    protected $serializer;

    /**
     * @var SerializationContext
     */
    protected $context;

    /**
     * @var string
     */
    protected $format;

    /**
     * @param BaseSerializer       $serializer
     * @param SerializationContext $context
     * @param string               $format
     */
    public function __construct(BaseSerializer $serializer, SerializationContext $context = null, $format = 'json')
    {
        $this->serializer = $serializer;
        $this->context = $context ?: SerializationContext::create();
        $this->format = $format;
    }

    /**
     * @inheritdoc
     */
    public function serialize($value)
    {
        return $this->serializer->serialize($value, $this->format, $this->context);
    }
}
