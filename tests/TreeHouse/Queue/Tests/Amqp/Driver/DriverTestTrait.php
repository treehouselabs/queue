<?php

namespace TreeHouse\Queue\Tests\Amqp\Driver;

use TreeHouse\Queue\Amqp\ChannelInterface;
use TreeHouse\Queue\Amqp\ConnectionInterface;
use TreeHouse\Queue\Amqp\ExchangeInterface;
use TreeHouse\Queue\Amqp\QueueInterface;
use TreeHouse\Queue\Exception\AmqpException;
use TreeHouse\Queue\Factory\FactoryInterface;

trait DriverTestTrait
{
    /**
     * @var CachedFactory
     */
    protected $factory;

    /**
     * @return FactoryInterface
     */
    abstract protected function getFactory();

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->factory = new CachedFactory($this->getFactory());
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        $this->factory->clear();
    }
}

class CachedFactory implements FactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $delegate;

    /**
     * @var ConnectionInterface[]
     */
    private $connections = [];

    /**
     * @var ChannelInterface[]
     */
    private $channels = [];

    /**
     * @var ExchangeInterface[]
     */
    private $exchanges = [];

    /**
     * @var QueueInterface[]
     */
    private $queues = [];

    /**
     * @param FactoryInterface $delegate
     */
    public function __construct(FactoryInterface $delegate)
    {
        $this->delegate = $delegate;
    }

    /**
     * @inheritdoc
     */
    public function createConnection($host, $port = 5672, $user = 'guest', $pass = 'guest', $vhost = '/')
    {
        $conn = $this->delegate->createConnection($host, $port, $user, $pass, $vhost);
        $this->connections[] = $conn;

        return $conn;
    }

    /**
     * @inheritdoc
     */
    public function createChannel(ConnectionInterface $connection)
    {
        $channel = $this->delegate->createChannel($connection);
        $this->channels[] = $channel;

        return $channel;
    }

    /**
     * @inheritdoc
     */
    public function createExchange(ChannelInterface $channel, $name, $type = ExchangeInterface::TYPE_DIRECT, $flags = null, array $args = [])
    {
        $exchg = $this->delegate->createExchange($channel, $name, $type, $flags, $args);
        $this->exchanges[] = $exchg;

        return $exchg;
    }

    /**
     * @inheritdoc
     */
    public function createQueue(ChannelInterface $channel, $name = null, $flags = null, array $args = [])
    {
        $queue = $this->delegate->createQueue($channel, $name, $flags, $args);
        $this->queues[] = $queue;

        return $queue;
    }

    public function clear()
    {
        foreach ($this->queues as $queue) {
            try {
                $queue->delete();
            } catch (AmqpException $e) {
            }
        }

        foreach ($this->exchanges as $exchg) {
            try {
                $exchg->delete();
            } catch (AmqpException $e) {
            }
        }

        foreach ($this->connections as $conn) {
            try {
                $conn->close();
            } catch (AmqpException $e) {
            }
        }

        $this->queues = [];
        $this->exchanges = [];
        $this->channels = [];
        $this->connections = [];
    }
}
