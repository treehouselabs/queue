<?php

namespace TreeHouse\Queue\Message\Composer;

use Doctrine\Common\Persistence\ManagerRegistry;
use TreeHouse\Queue\Message\Serializer\SerializerInterface;

class DoctrineMessageComposer extends DefaultMessageComposer
{
    /**
     * @var ManagerRegistry
     */
    protected $doctrine;

    /**
     * @var string
     */
    protected $className;

    /**
     * @param ManagerRegistry     $doctrine
     * @param SerializerInterface $serializer
     * @param string              $className
     */
    public function __construct(ManagerRegistry $doctrine, SerializerInterface $serializer, $className)
    {
        $this->doctrine  = $doctrine;
        $this->className = $className;

        parent::__construct($serializer);
    }

    /**
     * @param mixed $payload
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    protected function serialize($payload)
    {
        // convert identifier to Doctrine object
        if (is_array($payload) || is_scalar($payload)) {
            // just use the id when no keys are given (Doctrine expects an array like [id: 1234]
            if (is_array($payload) && is_numeric(key($payload))) {
                $payload = current($payload);
            }

            $payload = $this->doctrine->getRepository($this->className)->find($payload);
        }

        // anything else is just wrong at this point
        if (!is_object($payload) || !($payload instanceof $this->className)) {
            throw new \RuntimeException(
                sprintf('Expecting object of type %s, but got %s', $this->className, var_export($payload, true))
            );
        }

        return parent::serialize($payload);
    }
}
