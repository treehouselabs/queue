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

    public function testConstructor()
    {
        $consumer = new Consumer($this->provider, $this->processor);

        $this->assertInstanceOf(Consumer::class, $consumer);
    }

    public function testConsume()
    {
        $message = new Message('test', null, uniqid());

        $this->provider->expects($this->at(0))->method('get')->will($this->returnValue($message));
        $this->provider->expects($this->at(1))->method('get')->will($this->returnValue($message));
        $this->provider->expects($this->at(2))->method('get')->will($this->returnValue(null));

        $this->provider->expects($this->exactly(3))->method('get');

        $this->processor
            ->expects($this->exactly(2))
            ->method('process')
        ;

        $consumer = new Consumer($this->provider, $this->processor);
        $consumer->consume();
    }

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
}
