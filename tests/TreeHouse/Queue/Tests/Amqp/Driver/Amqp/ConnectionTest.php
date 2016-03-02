<?php

namespace TreeHouse\Queue\Tests\Amqp\Driver\Amqp;

use Mockery as Mock;
use Mockery\MockInterface;
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
        /** @var MockInterface|\AMQPConnection $delegate */
        $delegate = Mock::mock(\AMQPConnection::class);
        $delegate->shouldReceive('connect');
        $delegate->shouldReceive('disconnect')->once();

        $conn = new Connection($delegate);
        $conn->connect();

        unset($conn);
    }
}
