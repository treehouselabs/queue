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
     * @var string[]
     */
    protected $groups;

    /**
     * @var string
     */
    protected $format;

    /**
     * @param BaseSerializer $serializer
     * @param string[]       $groups
     * @param string         $format
     */
    public function __construct(BaseSerializer $serializer, array $groups = [], $format = 'json')
    {
        $this->serializer = $serializer;
        $this->groups     = $groups;
        $this->format     = $format;
    }

    /**
     * @inheritdoc
     */
    public function serialize($value)
    {
        $context = SerializationContext::create();
        if (!empty($this->groups)) {
            $context->setGroups($this->groups);
        }

        return $this->serializer->serialize($value, $this->format, $context);
    }
}
