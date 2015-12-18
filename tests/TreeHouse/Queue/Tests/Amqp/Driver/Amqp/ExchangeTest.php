<?php

namespace TreeHouse\Queue\Tests\Amqp\Driver\Amqp;

use TreeHouse\Queue\Amqp\ChannelInterface;
use TreeHouse\Queue\Amqp\Driver\Amqp\Exchange;
use TreeHouse\Queue\Tests\Amqp\Driver\AbstractDriverExchangeTest;

class ExchangeTest extends AbstractDriverExchangeTest
{
    use AmqpFactoryTestTrait;

    /**
     * @inheritdoc
     */
    protected function getDelegate(ChannelInterface $channel)
    {
        return new \AMQPExchange($channel->getDelegate());
    }

    /**
     * @test
     */
    public function flags_are_converted()
    {
        foreach ($this->getFlags() as $flag) {
            $this->assertSame($flag, Exchange::convertFromDelegateFlags(Exchange::convertToDelegateFlags($flag)));
        }
    }
}
