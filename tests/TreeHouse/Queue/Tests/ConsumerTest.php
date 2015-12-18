<?php

namespace TreeHouse\Queue\Tests;

use TreeHouse\Queue\Consumer;
use TreeHouse\Queue\Message\Message;
use TreeHouse\Queue\Message\Provider\MessageProviderInterface;
use TreeHouse\Queue\Processor\ProcessorInterface;

class ConsumerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|MessageProviderInterface
     */
    protected $provider;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ProcessorInterface
     */
    protected $processor;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->provider = $this
            ->getMockBuilder(MessageProviderInterface::class)
            ->getMockForAbstractClass()
        ;

        $this->processor = $this
            ->getMockBuilder(ProcessorInterface::class)
            ->getMockForAbstractClass()
        ;
    }

    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $consumer = new Consumer($this->provider, $this->processor);

        $this->assertInstanceOf(Consumer::class, $consumer);
    }

    /**
     * @test
     */
    public function it_can_consume_messages()
    {
        $messages = [
            new Message('test', null, uniqid()),
            new Message('test', null, uniqid()),
        ];

        // keep returning messages until we run out of them
        $this->provider->expects($this->any())->method('get')->will($this->returnCallback(function () use (&$messages) {
            return empty($messages) ? null : array_shift($messages);
        }));

        $this->provider->expects($this->exactly(sizeof($messages) + 1))->method('get');
        $this->processor->expects($this->exactly(sizeof($messages)))->method('process');

        $consumer = new Consumer($this->provider, $this->processor);
        $consumer->consume();
    }
}
