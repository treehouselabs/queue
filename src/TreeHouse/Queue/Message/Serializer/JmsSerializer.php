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
     * @param string               $format
     * @param SerializationContext $context
     */
    public function __construct(BaseSerializer $serializer, $format = 'json', SerializationContext $context = null)
    {
        $this->serializer = $serializer;
        $this->format = $format;
        $this->context = $context ?: SerializationContext::create();
    }

    /**
     * @inheritdoc
     */
    public function serialize($value)
    {
        return $this->serializer->serialize($value, $this->format, $this->context);
    }
}
