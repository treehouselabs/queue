<?php

namespace TreeHouse\Queue\Message\Serializer;

use Doctrine\Common\Persistence\ManagerRegistry;

class DoctrineSerializer implements SerializerInterface
{
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @inheritdoc
     */
    public function serialize($value)
    {
        if (is_numeric($value)) {
            return $value;
        }
    }
}
