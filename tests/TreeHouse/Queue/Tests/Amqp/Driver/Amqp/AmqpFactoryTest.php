<?php

namespace TreeHouse\Queue\Tests\Amqp\Driver\Amqp;

use TreeHouse\Queue\Amqp\Driver\Amqp\AmqpFactory;
use TreeHouse\Queue\Tests\Amqp\Driver\AbstractFactoryTest;

class AmqpFactoryTest extends AbstractFactoryTest
{
    /**
     * @return string
     */
    protected function getFactoryClass()
    {
        return AmqpFactory::class;
    }
}
