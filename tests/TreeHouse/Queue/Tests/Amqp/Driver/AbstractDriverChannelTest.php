<?php

namespace TreeHouse\Queue\Tests\Amqp\Driver;

use PHPUnit_Framework_Assert as Assert;
use TreeHouse\Queue\Amqp\ChannelInterface;
use TreeHouse\Queue\Amqp\ConnectionInterface;

abstract class AbstractDriverChannelTest extends \PHPUnit_Framework_TestCase
{
    use DriverTestTrait;

    /**
     * @test
     */
    public function it_can_be_created()
    {
        $conn = $this->factory->createConnection('localhost');
        $channel = $this->factory->createChannel($conn);

        Assert::assertInstanceOf(ChannelInterface::class, $channel);
    }

    /**
     * @test
     */
    public function it_can_return_its_connection()
    {
        $conn = $this->factory->createConnection('localhost');
        $channel = $this->factory->createChannel($conn);

        Assert::assertSame($conn, $channel->getConnection());
    }

    /**
     * @test
     */
    public function it_can_return_its_id()
    {
        $conn = $this->factory->createConnection('localhost');
        $channel = $this->factory->createChannel($conn);

        Assert::assertInternalType('integer', $channel->getChannelId());
        Assert::assertGreaterThan(0, $channel->getChannelId());
    }

    /**
     * @test
     */
    public function it_can_set_qos()
    {
        $conn = $this->factory->createConnection('localhost');
        $channel = $this->factory->createChannel($conn);

        Assert::assertTrue($channel->basicQos(0, 5678));
    }

    /**
     * @test
     * @expectedException \TreeHouse\Queue\Exception\ConnectionException
     */
    public function it_cannot_set_qos_on_a_closed_connection()
    {
        $conn = $this->factory->createConnection('localhost');
        $channel = $this->factory->createChannel($conn);
        $conn->close();

        $channel->basicQos(0, 5678);
    }

    /**
     * @test
     */
    public function it_can_start_a_transaction()
    {
        $conn = $this->factory->createConnection('localhost');
        $channel = $this->factory->createChannel($conn);

        Assert::assertTrue($channel->startTransaction());
    }

    /**
     * @test
     * @expectedException \TreeHouse\Queue\Exception\ChannelException
     */
    public function it_cannot_start_a_transaction_on_a_closed_connection()
    {
        $conn = $this->factory->createConnection('localhost');
        $channel = $this->factory->createChannel($conn);
        $conn->close();

        $channel->startTransaction();
    }

    /**
     * @test
     */
    public function it_can_commit_a_transaction()
    {
        $conn = $this->factory->createConnection('localhost');
        $channel = $this->factory->createChannel($conn);

        $channel->startTransaction();

        Assert::assertTrue($channel->commitTransaction());
    }

    /**
     * @test
     * @expectedException \TreeHouse\Queue\Exception\ChannelException
     */
    public function it_cannot_commit_an_unstarted_transaction()
    {
        $conn = $this->factory->createConnection('localhost');
        $channel = $this->factory->createChannel($conn);
        $channel->commitTransaction();
    }

    /**
     * @test
     * @expectedException \TreeHouse\Queue\Exception\ChannelException
     */
    public function it_cannot_commit_a_transaction_on_a_closed_connection()
    {
        $conn = $this->factory->createConnection('localhost');
        $channel = $this->factory->createChannel($conn);
        $conn->close();

        $channel->commitTransaction();
    }

    /**
     * @test
     * @expectedException \TreeHouse\Queue\Exception\ChannelException
     */
    public function it_cannot_rollback_an_unstarted_transaction()
    {
        $conn = $this->factory->createConnection('localhost');
        $channel = $this->factory->createChannel($conn);
        $channel->rollbackTransaction();
    }

    /**
     * @test
     * @expectedException \TreeHouse\Queue\Exception\ChannelException
     */
    public function it_cannot_rollback_a_ransaction_on_a_closed_connection()
    {
        $conn = $this->factory->createConnection('localhost');
        $channel = $this->factory->createChannel($conn);
        $conn->close();

        $channel->rollbackTransaction();
    }

    /**
     * @test
     */
    public function it_can_return_its_delegate()
    {
        $conn = $this->factory->createConnection('localhost');
        $channel = $this->factory->createChannel($conn);

        $this->assertEquals($this->getDelegate($conn), $channel->getDelegate());
    }

    /**
     * @param ConnectionInterface $conn
     *
     * @return mixed
     */
    abstract protected function getDelegate(ConnectionInterface $conn);
}
