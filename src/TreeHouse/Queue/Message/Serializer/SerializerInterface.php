<?php

namespace TreeHouse\Queue\Message\Serializer;

interface SerializerInterface
{
    /**
     * @param mixed $value
     *
     * @return string
     */
    public function serialize($value);
}
