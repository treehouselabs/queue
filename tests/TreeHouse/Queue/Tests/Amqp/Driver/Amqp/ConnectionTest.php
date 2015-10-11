<?php

namespace TreeHouse\Queue\Tests\Amqp\Driver\Amqp;

use TreeHouse\Queue\Amqp\Driver\Amqp\Connection;
use TreeHouse\Queue\Tests\Amqp\Driver\AbstractDriverConnectionTest;

class ConnectionTest extends AbstractDriverConnectionTest
{
    use AmqpFactoryTestTrait;

    /**
     * @inheritdoc
     */
    public function test_that_it_closes_on_destruct()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\AMQPConnection $delegate */
        $delegate = $this->getMockBuilder(\AMQPConnection::class)->setMethods(['disconnect'])->getMock();
        $delegate->expects($this->once())->method('disconnect');

        $conn = new Connection($delegate);
        $conn->connect();

        unset($conn);
    }
}
