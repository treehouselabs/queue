<?php

namespace TreeHouse\Queue\Tests\Amqp\Driver;

use TreeHouse\Queue\Amqp\ChannelInterface;
use TreeHouse\Queue\Amqp\ConnectionInterface;
use TreeHouse\Queue\Amqp\ExchangeInterface;
use TreeHouse\Queue\Factory\FactoryInterface;

abstract class AbstractFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @return string
     */
    abstract protected function getFactoryClass();

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $class = $this->getFactoryClass();
        $this->factory = new $class();
    }

    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $this->assertInstanceOf($this->getFactoryClass(), $this->factory);
    }

    /**
     * @test
     */
    public function it_can_create_a_connection()
    {
        $connection = $this->factory->createConnection(
            'localhost',
            1234,
            'foo_user',
            'foo_pass',
            '/foo'
        );

        $this->assertInstanceOf(ConnectionInterface::class, $connection);
        $this->assertEquals('localhost', $connection->getHost());
        $this->assertEquals(1234, $connection->getPort());
        $this->assertEquals('foo_user', $connection->getLogin());
        $this->assertEquals('foo_pass', $connection->getPassword());
        $this->assertEquals('/foo', $connection->getVhost());
    }

    /**
     * @test
     */
    public function it_can_create_a_channel()
    {
        $connection = $this->factory->createConnection('localhost');
        $channel = $this->factory->createChannel($connection);

        $this->assertInstanceOf(ChannelInterface::class, $channel);

        return $channel;
    }

    /**
     * @test
     */
    public function it_can_create_an_exchange()
    {
        $connection = $this->factory->createConnection('localhost');
        $channel = $this->factory->createChannel($connection);
        $exchange = $this->factory->createExchange($channel, 'xchg1');

        $this->assertInstanceOf(ExchangeInterface::class, $exchange);
        $this->assertEquals('xchg1', $exchange->getName());
        $this->assertEquals(ExchangeInterface::TYPE_DIRECT, $exchange->getType());

        // create exchange with different type
        $exchange = $this->factory->createExchange($channel, 'xchg2', ExchangeInterface::TYPE_FANOUT);
        $this->assertEquals(ExchangeInterface::TYPE_FANOUT, $exchange->getType());
    }
}
