<?php

namespace TreeHouse\Queue\Tests\Amqp\Driver;

use PHPUnit_Framework_Assert as Assert;
use TreeHouse\Queue\Amqp\ConnectionInterface;

abstract class AbstractDriverConnectionTest extends \PHPUnit_Framework_TestCase
{
    use DriverTestTrait;

    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $conn = $this->factory->createConnection('localhost');

        Assert::assertInstanceOf(ConnectionInterface::class, $conn);
    }

    /**
     * @test
     */
    public function it_can_set_a_host()
    {
        $conn = $this->factory->createConnection('localhost');

        Assert::assertTrue($conn->setHost('otherhost'));
        Assert::assertEquals('otherhost', $conn->getHost());
    }

    /**
     * @test
     */
    public function it_can_set_a_login()
    {
        $conn = $this->factory->createConnection('localhost');

        Assert::assertTrue($conn->setLogin('guest'));
        Assert::assertEquals('guest', $conn->getLogin());
    }

    /**
     * @test
     */
    public function it_can_set_a_password()
    {
        $conn = $this->factory->createConnection('localhost');

        Assert::assertTrue($conn->setPassword('supers3cr4t'));
        Assert::assertEquals('supers3cr4t', $conn->getPassword());
    }

    /**
     * @test
     */
    public function it_can_set_the_port()
    {
        $conn = $this->factory->createConnection('localhost');

        Assert::assertTrue($conn->setPort(1234));
        Assert::assertEquals(1234, $conn->getPort());
    }

    /**
     * @test
     */
    public function it_can_set_the_vhost()
    {
        $conn = $this->factory->createConnection('localhost');

        Assert::assertTrue($conn->setVhost('/foo'));
        Assert::assertEquals('/foo', $conn->getVhost());
    }

    /**
     * @test
     * @expectedException \TreeHouse\Queue\Exception\ConnectionException
     */
    public function it_cannot_set_a_too_long_host()
    {
        // exception when host exceeds 1024 characters
        $conn = $this->factory->createConnection('localhost');
        $conn->setHost(str_repeat('a', 1025));
    }

    /**
     * @test
     * @expectedException \TreeHouse\Queue\Exception\ConnectionException
     */
    public function it_cannot_set_a_too_long_login()
    {
        // exception when login exceeds 128 characters
        $conn = $this->factory->createConnection('localhost');
        $conn->setLogin(str_repeat('a', 129));
    }

    /**
     * @test
     * @expectedException \TreeHouse\Queue\Exception\ConnectionException
     */
    public function it_cannot_set_a_too_long_password()
    {
        // exception when password exceeds 128 characters
        $conn = $this->factory->createConnection('localhost');
        $conn->setPassword(str_repeat('a', 129));
    }

    /**
     * @test
     * @expectedException \TreeHouse\Queue\Exception\ConnectionException
     */
    public function it_cannot_set_a_too_high_port()
    {
        // exception when port number is too high
        $conn = $this->factory->createConnection('localhost');
        $conn->setPort(65536);
    }

    /**
     * @test
     * @expectedException \TreeHouse\Queue\Exception\ConnectionException
     */
    public function it_cannot_set_a_too_long_vhost()
    {
        // exception when vhost exceeds 128 characters
        $conn = $this->factory->createConnection('localhost');
        $conn->setVhost(str_repeat('a', 129));
    }

    /**
     * @test
     * @functional
     */
    public function it_can_connect_and_close()
    {
        $conn = $this->factory->createConnection('localhost');

        Assert::assertTrue($conn->connect());
        Assert::assertTrue($conn->isConnected());
        Assert::assertTrue($conn->close());
        Assert::assertFalse($conn->isConnected());
    }

    /**
     * @test
     * @functional
     */
    public function it_cannot_close_when_not_opened()
    {
        $conn = $this->factory->createConnection('localhost');

        Assert::assertTrue($conn->close(), 'Calling close on a non-open connection does not raise an error');
    }

    /**
     * @test
     * @functional
     */
    public function it_can_reconnect()
    {
        $conn = $this->factory->createConnection('localhost');

        Assert::assertTrue($conn->connect());
        Assert::assertTrue($conn->isConnected());
        Assert::assertTrue($conn->reconnect());
        Assert::assertTrue($conn->isConnected());
    }

    /**
     * Tests that a driver properly closes its connection when destructed.
     *
     * @test
     */
    abstract public function test_that_it_closes_on_destruct();
}
