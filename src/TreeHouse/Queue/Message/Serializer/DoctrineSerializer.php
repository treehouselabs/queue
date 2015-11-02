<?php

namespace TreeHouse\Queue\Message\Serializer;

use Doctrine\Common\Persistence\ManagerRegistry;

class DoctrineSerializer implements SerializerInterface
{
    /**
     * @var ManagerRegistry
     */
    protected $doctrine;

    /**
     * @param ManagerRegistry $doctrine
     */
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
     * @param object $value
     *
     * @throws \InvalidArgumentException When $value is not a Doctrine object
     *
     * @return array
     */
    protected function getIdentifierValues($value)
    {
        if (!is_object($value)) {
            throw new \InvalidArgumentException('Only Doctrine objects can be serialized');
        }

        $class = get_class($value);
        $metadata = $this->doctrine->getManager()->getClassMetadata($class);

        return $metadata->getIdentifierValues($value);
    }
}
