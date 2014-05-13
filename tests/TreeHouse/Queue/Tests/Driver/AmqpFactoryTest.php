<?php

namespace TreeHouse\Queue\Tests\Driver;

use TreeHouse\Queue\Factory\AmqpFactory;

class AmqpFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AmqpFactory
     */
    protected $factory;

    public function testConstructor()
    {
        $this->assertInstanceOf(AmqpFactory::class, new AmqpFactory());
    }

    public function testCreateConnection()
    {
        $connection = $this->factory->createConnection('localhost');

        $this->assertInstanceOf(\AMQPConnection::class, $connection);

        return $connection;
    }

    public function testConnectionParameters()
    {
        $connection = $this->factory->createConnection(
            'localhost',
            1234,
            'foo_user',
            'foo_pass',
            '/foo'
        );

        $this->assertInstanceOf(\AMQPConnection::class, $connection);
        $this->assertEquals('localhost', $connection->getHost());
        $this->assertEquals(1234, $connection->getPort());
        $this->assertEquals('foo_user', $connection->getLogin());
        $this->assertEquals('foo_pass', $connection->getPassword());
        $this->assertEquals('/foo', $connection->getVhost());

        return $connection;
    }

    /**
     * @depends testCreateConnection
     */
    public function testCreateChannel(\AMQPConnection $connection)
    {
        $channel = $this->factory->createChannel($connection);

        $this->assertInstanceOf(\AMQPChannel::class, $channel);

        return $channel;
    }

    /**
     * @depends testCreateChannel
     */
    public function testCreateExchange(\AMQPChannel $channel)
    {
        $exchange = $this->factory->createExchange($channel, 'xchg1');

        $this->assertInstanceOf(\AMQPExchange::class, $exchange);
        $this->assertEquals('xchg1', $exchange->getName());
        $this->assertEquals(AMQP_EX_TYPE_DIRECT, $exchange->getType());

        // create exchange with different type
        $exchange = $this->factory->createExchange($channel, 'xchg2', AMQP_EX_TYPE_FANOUT);
        $this->assertEquals(AMQP_EX_TYPE_FANOUT, $exchange->getType());
    }

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->factory = new AmqpFactory();
    }
}
