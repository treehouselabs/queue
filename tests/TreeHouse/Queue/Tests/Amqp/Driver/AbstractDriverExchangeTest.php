<?php

namespace TreeHouse\Queue\Tests\Amqp\Driver;

use TreeHouse\Queue\Amqp\ChannelInterface;
use TreeHouse\Queue\Amqp\ExchangeInterface;

abstract class AbstractDriverExchangeTest extends \PHPUnit_Framework_TestCase
{
    use DriverTestTrait;

    /**
     * @test
     */
    public function it_can_be_created()
    {
        $name = 'test';
        $type = ExchangeInterface::TYPE_FANOUT;
        $flags = ExchangeInterface::AUTODELETE | ExchangeInterface::INTERNAL;
        $args = ['foo' => 'bar'];

        $conn = $this->factory->createConnection('localhost');
        $channel = $this->factory->createChannel($conn);
        $exchange = $this->factory->createExchange($channel, $name, $type, $flags, $args);

        $this->assertInstanceOf(ExchangeInterface::class, $exchange);
        $this->assertSame($channel, $exchange->getChannel());
        $this->assertSame($conn, $exchange->getConnection());
        $this->assertEquals($name, $exchange->getName());
        $this->assertEquals($type, $exchange->getType());
        $this->assertEquals($flags, $exchange->getFlags());
        $this->assertEquals($args, $exchange->getArguments());
        $this->assertEquals('bar', $exchange->getArgument('foo'));
    }

    /**
     * @test
     */
    public function it_can_bind_and_unbind()
    {
        $conn = $this->factory->createConnection('localhost');
        $channel = $this->factory->createChannel($conn);

        $exchange1 = $this->factory->createExchange($channel, 'exchg1');
        $exchange2 = $this->factory->createExchange($channel, 'exchg2');
        $exchange1->declareExchange();
        $exchange2->declareExchange();

        $routingKey = 'test';

        $this->assertTrue($exchange1->bind($exchange2->getName(), $routingKey));
        $this->assertTrue($exchange1->unbind($exchange2->getName(), $routingKey));
    }

    /**
     * @test
     * @expectedException \TreeHouse\Queue\Exception\ExchangeException
     */
    public function it_cannot_bind()
    {
        $conn = $this->factory->createConnection('localhost');
        $channel = $this->factory->createChannel($conn);
        $exchange = $this->factory->createExchange($channel, 'exchg1');

        $exchange->bind('exchg3', 'test');
    }

    /**
     * @test
     * @expectedException \TreeHouse\Queue\Exception\ChannelException
     */
    public function it_cannot_bind_on_closed_channel()
    {
        $conn = $this->factory->createConnection('localhost');
        $channel = $this->factory->createChannel($conn);
        $exchange = $this->factory->createExchange($channel, 'exchg1');
        $exchange->declareExchange();

        $conn->close();

        $exchange->bind('exchg1', 'test');
    }

    /**
     * @test
     * @expectedException \TreeHouse\Queue\Exception\ConnectionException
     */
    public function it_cannot_bind_on_closed_connection()
    {
        $this->markTestIncomplete('Find out how to set up this case');
    }

    /**
     * @test
     * @expectedException \TreeHouse\Queue\Exception\ExchangeException
     */
    public function it_cannot_unbind()
    {
        $this->markTestIncomplete('Find out how to set up this case');
    }

    /**
     * @test
     * @expectedException \TreeHouse\Queue\Exception\ChannelException
     */
    public function it_cannot_unbind_on_closed_channel()
    {
        $conn = $this->factory->createConnection('localhost');
        $channel = $this->factory->createChannel($conn);
        $exchange1 = $this->factory->createExchange($channel, 'exchg1');
        $exchange2 = $this->factory->createExchange($channel, 'exchg2');

        $exchange1->declareExchange();
        $exchange2->declareExchange();

        $exchange1->bind($exchange2->getName(), 'test');

        $conn->close();

        $exchange1->unbind('exchg1', 'test');
    }

    /**
     * @test
     * @expectedException \TreeHouse\Queue\Exception\ConnectionException
     */
    public function it_cannot_unbind_on_closed_connection()
    {
        $this->markTestIncomplete('Find out how to set up this case');
    }

    /**
     * @test
     */
    public function it_can_declare_itself()
    {
        $conn = $this->factory->createConnection('localhost');
        $channel = $this->factory->createChannel($conn);
        $exchange = $this->factory->createExchange($channel, 'exchg1');

        $this->assertTrue($exchange->declareExchange());
    }

    /**
     * @test
     * @expectedException \TreeHouse\Queue\Exception\ChannelException
     */
    public function it_cannot_be_declared_on_closed_channel()
    {
        $conn = $this->factory->createConnection('localhost');
        $channel = $this->factory->createChannel($conn);
        $exchange = $this->factory->createExchange($channel, 'exchg1');

        $conn->close();

        $this->assertTrue($exchange->declareExchange());
    }

    /**
     * @test
     */
    public function it_can_delete_itself()
    {
        $conn = $this->factory->createConnection('localhost');
        $channel = $this->factory->createChannel($conn);
        $exchange = $this->factory->createExchange($channel, 'exchg1');

        $this->assertTrue($exchange->delete());
    }

    /**
     * @test
     */
    public function it_can_publish_itself()
    {
        $conn = $this->factory->createConnection('localhost');
        $channel = $this->factory->createChannel($conn);
        $exchange = $this->factory->createExchange($channel, 'exchg1');

        $this->assertTrue($exchange->publish('foo'));
    }

    /**
     * @return array
     */
    protected function getFlags()
    {
        return [
            ExchangeInterface::NOPARAM,
            ExchangeInterface::DURABLE,
            ExchangeInterface::PASSIVE,
            ExchangeInterface::AUTODELETE,
            ExchangeInterface::INTERNAL,
            ExchangeInterface::IFUNUSED,
            ExchangeInterface::MANDATORY,
            ExchangeInterface::IMMEDIATE,
            ExchangeInterface::NOWAIT,
        ];
    }

    /**
     * @param ChannelInterface $channel
     *
     * @return mixed
     */
    abstract protected function getDelegate(ChannelInterface $channel);
}
