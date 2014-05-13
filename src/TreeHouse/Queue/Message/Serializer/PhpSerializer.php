<?php

namespace TreeHouse\Queue\Message\Serializer;

class PhpSerializer implements SerializerInterface
{
    /**
     * @inheritdoc
     */
    public function serialize($value)
    {
        if (!is_string($value)) {
            $value = serialize($value);
        }

        return $value;
    }
}
