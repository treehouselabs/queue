<?php

namespace TreeHouse\Queue\Message\Serializer;

class JsonSerializer implements SerializerInterface
{
    /**
     * @inheritdoc
     */
    public function serialize($value)
    {
        if (!is_string($value)) {
            $value = json_encode($value);
        }

        return $value;
    }
}
