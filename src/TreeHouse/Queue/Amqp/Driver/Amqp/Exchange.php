<?php

namespace TreeHouse\Queue\Amqp\Driver\Amqp;

use TreeHouse\Queue\Amqp\ChannelInterface;
use TreeHouse\Queue\Amqp\ExchangeInterface;
use TreeHouse\Queue\Exception\ChannelException;
use TreeHouse\Queue\Exception\ConnectionException;
use TreeHouse\Queue\Exception\ExchangeException;

class Exchange implements ExchangeInterface
{
    /**
     * @var \AMQPExchange
     */
    protected $delegate;

    /**
     * @var ChannelInterface
     */
    protected $channel;

    /**
     * @var array<integer, integer>
     */
    protected static $flagMap = [
        self::NOPARAM => AMQP_NOPARAM,
        self::DURABLE => AMQP_DURABLE,
        self::PASSIVE => AMQP_PASSIVE,
        self::AUTODELETE => AMQP_AUTODELETE,
        self::INTERNAL => AMQP_INTERNAL,
        self::IFUNUSED => AMQP_IFUNUSED,
        self::MANDATORY => AMQP_MANDATORY,
        self::IMMEDIATE => AMQP_IMMEDIATE,
        self::NOWAIT => AMQP_NOWAIT,
    ];

    /**
     * @param \AMQPExchange    $delegate
     * @param ChannelInterface $channel
     */
    public function __construct(\AMQPExchange $delegate, ChannelInterface &$channel)
    {
        $this->delegate = $delegate;
        $this->channel = &$channel;
    }

    /**
     * @inheritdoc
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @inheritdoc
     */
    public function getConnection()
    {
        return $this->channel->getConnection();
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->delegate->getName();
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->delegate->getType();
    }

    /**
     * @inheritdoc
     */
    public function getFlags()
    {
        return self::convertFromDelegateFlags($this->delegate->getFlags());
    }

    /**
     * @inheritdoc
     */
    public function getArgument($key)
    {
        return $this->delegate->getArgument($key);
    }

    /**
     * @inheritdoc
     */
    public function getArguments()
    {
        return $this->delegate->getArguments();
    }

    /**
     * @inheritdoc
     */
    public function bind($exchangeName, $routingKey, array $arguments = [])
    {
        try {
            return $this->delegate->bind($exchangeName, $routingKey, $arguments);
        } catch (\AMQPExchangeException $e) {
            throw new ExchangeException($e->getMessage(), $e->getCode(), $e);
        } catch (\AMQPChannelException $e) {
            throw new ChannelException($e->getMessage(), $e->getCode(), $e);
        } catch (\AMQPConnectionException $e) {
            throw new ConnectionException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function unbind($exchangeName, $routingKey, array $arguments = [])
    {
        try {
            return $this->delegate->unbind($exchangeName, $routingKey, $arguments);
        } catch (\AMQPExchangeException $e) {
            throw new ExchangeException($e->getMessage(), $e->getCode(), $e);
        } catch (\AMQPChannelException $e) {
            throw new ChannelException($e->getMessage(), $e->getCode(), $e);
        } catch (\AMQPConnectionException $e) {
            throw new ConnectionException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function declareExchange()
    {
        try {
            return $this->delegate->declareExchange();
        } catch (\AMQPExchangeException $e) {
            throw new ExchangeException($e->getMessage(), $e->getCode(), $e);
        } catch (\AMQPChannelException $e) {
            throw new ChannelException($e->getMessage(), $e->getCode(), $e);
        } catch (\AMQPConnectionException $e) {
            throw new ConnectionException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function delete($exchangeName = null, $flags = null)
    {
        try {
            return $this->delegate->delete($exchangeName, self::convertToDelegateFlags($flags));
        } catch (\AMQPExchangeException $e) {
            throw new ExchangeException($e->getMessage(), $e->getCode(), $e);
        } catch (\AMQPChannelException $e) {
            throw new ChannelException($e->getMessage(), $e->getCode(), $e);
        } catch (\AMQPConnectionException $e) {
            throw new ConnectionException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function publish($message, $routingKey = null, $flags = null, array $attributes = [])
    {
        try {
            return $this->delegate->publish($message, $routingKey, self::convertToDelegateFlags($flags), $attributes);
        } catch (\AMQPExchangeException $e) {
            throw new ExchangeException($e->getMessage(), $e->getCode(), $e);
        } catch (\AMQPChannelException $e) {
            throw new ChannelException($e->getMessage(), $e->getCode(), $e);
        } catch (\AMQPConnectionException $e) {
            throw new ConnectionException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @inheritdoc
     *
     * @return \AMQPExchange
     */
    public function getDelegate()
    {
        return $this->delegate;
    }

    /**
     * @param int|null $flags
     *
     * @return int
     */
    public static function convertToDelegateFlags($flags = null)
    {
        if (null === $flags) {
            return AMQP_NOPARAM;
        }

        $converted = 0;

        foreach (self::$flagMap as $from => $to) {
            if ($flags & $from) {
                $converted |= $to;
            }
        }

        return $converted;
    }

    /**
     * @param int|null $flags
     *
     * @return int
     */
    public static function convertFromDelegateFlags($flags = null)
    {
        if (null === $flags) {
            return self::NOPARAM;
        }

        $converted = 0;

        foreach (self::$flagMap as $from => $to) {
            if ($flags & $to) {
                $converted |= $from;
            }
        }

        return $converted;
    }
}
