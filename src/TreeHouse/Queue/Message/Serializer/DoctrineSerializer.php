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
        return json_encode($this->getIdentifierValues($value));
    }

    /**
     * @param $value
     *
     * @return array
     */
    protected function getIdentifierValues($value)
    {
        // if a raw identifier is passed, return it in an array.
        // this would be the same if we passed in an object with that id
        if (is_numeric($value)) {
            return [$value];
        }

        $class    = get_class($value);
        $metadata = $this->doctrine->getManager()->getClassMetadata($class);
        $id       = $metadata->getIdentifierValues($value);

        return array_values($id);
    }
}
