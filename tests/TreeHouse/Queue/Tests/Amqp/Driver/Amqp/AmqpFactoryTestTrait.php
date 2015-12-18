<?php

namespace TreeHouse\Queue\Tests\Amqp\Driver\Amqp;

use TreeHouse\Queue\Amqp\Driver\Amqp\AmqpFactory;

trait AmqpFactoryTestTrait
{
    /**
     * @inheritdoc
     */
    protected function getFactory()
    {
        return new AmqpFactory();
    }

    /**
     * @inheritdoc
     */
    protected function assertPreConditions()
    {
        if (!extension_loaded('amqp')) {
            $this->markTestSkipped('AMQP extension not loaded');
        }
    }

    abstract public function markTestSkipped($message = '');
}
