<?php

namespace TreeHouse\Queue\Amqp\Driver\Amqp;

use TreeHouse\Queue\Amqp\ChannelInterface;
use TreeHouse\Queue\Amqp\QueueInterface;

class Queue implements QueueInterface
{
    /**
     * @var \AMQPQueue
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
        self::EXCLUSIVE => AMQP_EXCLUSIVE,
        self::AUTODELETE => AMQP_AUTODELETE,
        self::MULTIPLE => AMQP_MULTIPLE,
        self::AUTOACK => AMQP_AUTOACK,
        self::REQUEUE => AMQP_REQUEUE,
        self::IFUNUSED => AMQP_IFUNUSED,
        self::IFEMPTY => AMQP_IFEMPTY,
    ];

    /**
     * @param \AMQPQueue       $delegate
     * @param ChannelInterface $channel
     */
    public function __construct(\AMQPQueue $delegate, ChannelInterface $channel)
    {
        $this->delegate = $delegate;
        $this->channel = $channel;
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
    public function setName($name)
    {
        return $this->delegate->setName($name);
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
    public function setArgument($key, $value)
    {
        return $this->delegate->setArgument($key, $value);
    }

    /**
     * @inheritdoc
     */
    public function setArguments(array $arguments)
    {
        return $this->delegate->setArguments($arguments);
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
    public function hasArgument($key)
    {
        return $this->delegate->hasArgument($key);
    }

    /**
     * @inheritDoc
     */
    public function setFlags($flags)
    {
        return $this->delegate->setFlags(self::convertToDelegateFlags($flags));
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
    public function getConsumerTag()
    {
        return $this->delegate->getConsumerTag();
    }

    /**
     * @inheritdoc
     */
    public function declareQueue()
    {
        return $this->delegate->declareQueue();
    }

    /**
     * @inheritdoc
     */
    public function bind($exchangeName, $routingKey, array $arguments = [])
    {
        return $this->delegate->bind($exchangeName, $routingKey, $arguments);
    }

    /**
     * @inheritdoc
     */
    public function unbind($exchangeName, $routingKey = null, array $arguments = [])
    {
        return $this->delegate->unbind($exchangeName, $routingKey, $arguments);
    }

    /**
     * @inheritdoc
     */
    public function get($flags = null)
    {
        if (false !== $envelope = $this->delegate->get(self::convertToDelegateFlags($flags))) {
            $envelope = new Envelope($envelope);
        }

        return $envelope;
    }

    /**
     * @inheritdoc
     */
    public function consume(callable $callback, $flags = null, $consumerTag = null)
    {
        $wrapper = function (\AMQPEnvelope $envelope) use ($callback) {
            $callback(new Envelope($envelope));

            return false;
        };

        $this->delegate->consume($wrapper, self::convertToDelegateFlags($flags), $consumerTag);
    }

    /**
     * @inheritdoc
     */
    public function ack($deliveryTag, $flags = null)
    {
        return $this->delegate->ack($deliveryTag, self::convertToDelegateFlags($flags));
    }

    /**
     * @inheritdoc
     */
    public function nack($deliveryTag, $flags = AMQP_NOPARAM)
    {
        $this->delegate->nack($deliveryTag, self::convertToDelegateFlags($flags));
    }

    /**
     * @inheritdoc
     */
    public function reject($deliveryTag, $flags = AMQP_NOPARAM)
    {
        $this->delegate->reject($deliveryTag, self::convertToDelegateFlags($flags));
    }

    /**
     * @inheritdoc
     */
    public function cancel($consumerTag = '')
    {
        return $this->delegate->cancel($consumerTag);
    }

    /**
     * @inheritdoc
     */
    public function delete($flags = null)
    {
        $this->delegate->delete(self::convertToDelegateFlags($flags));
    }

    /**
     * @inheritdoc
     */
    public function purge()
    {
        $this->delegate->purge();
    }

    /**
     * @inheritdoc
     *
     * @return \AMQPQueue
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
