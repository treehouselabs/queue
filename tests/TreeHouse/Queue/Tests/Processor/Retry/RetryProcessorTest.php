<?php

namespace TreeHouse\Queue\Tests\Processor\Retry;

use TreeHouse\Queue\Message\Message;
use TreeHouse\Queue\Processor\ProcessorInterface;
use TreeHouse\Queue\Processor\Retry\RetryProcessor;
use TreeHouse\Queue\Processor\Retry\RetryStrategyInterface;

class RetryProcessorTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $processor = new RetryProcessor($this->getProcessorMock(), $this->getStrategyMock());

        $this->assertInstanceOf(RetryProcessor::class, $processor);
    }

    public function testGettersAndSetters()
    {
        $processor = new RetryProcessor($this->getProcessorMock(), $this->getStrategyMock());

        $processor->setCooldownTime(1000);
        $this->assertEquals(1000, $processor->getCooldownTime());

        $processor->setMaxAttempts(5);
        $this->assertEquals(5, $processor->getMaxAttempts());

        $processor2 = new RetryProcessor($this->getProcessorMock(), $this->getStrategyMock());
        $processor->setProcessor($processor2);
        $this->assertSame($processor2, $processor->getProcessor());
    }

    public function testProcessSuccess()
    {
        $inner    = $this->getProcessorMock();
        $strategy = $this->getStrategyMock();

        /** @var RetryProcessor|\PHPUnit_Framework_MockObject_MockObject $processor */
        $processor = $this
            ->getMockBuilder(RetryProcessor::class)
            ->setConstructorArgs([$inner, $strategy])
            ->setMethods(['retryMessage'])
            ->getMock()
        ;

        $processor->expects($this->never())->method('retryMessage');
        $inner->expects($this->once())->method('process')->will($this->returnValue(true));

        $processor->process(new Message('test'));
    }

    public function testProcessFailed()
    {
        $inner    = $this->getProcessorMock();
        $strategy = $this->getStrategyMock();

        $processor = new RetryProcessor($inner, $strategy);

        $strategy->expects($this->once())->method('retry')->will($this->returnValue(false));
        $inner->expects($this->once())->method('process')->will($this->returnValue(false));

        $result = $processor->process(new Message('test'));

        $this->assertFalse($result, 'The ->process() method should return the value from the strategy');
    }

    public function testProcessException()
    {
        $inner    = $this->getProcessorMock();
        $strategy = $this->getStrategyMock();

        $processor = new RetryProcessor($inner, $strategy);

        $strategy->expects($this->once())->method('retry')->will($this->returnValue(true));
        $inner->expects($this->once())->method('process')->will($this->throwException(new \Exception()));

        $result = $processor->process(new Message('test'));

        $this->assertTrue($result, 'The ->process() method should return the value from the strategy');
    }

    /**
     * @expectedException \TreeHouse\Queue\Exception\ProcessExhaustedException
     */
    public function testMaxRetries()
    {
        $inner    = $this->getProcessorMock();
        $strategy = $this->getStrategyMock();

        $inner->expects($this->any())->method('process')->will($this->returnValue(false));

        // create message for second attempt
        $message = new Message('test');
        $message->getProperties()->set(RetryProcessor::PROPERTY_KEY, 2);

        $processor = new RetryProcessor($inner, $strategy);
        $processor->setMaxAttempts(2);
        $processor->process($message);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ProcessorInterface
     */
    protected function getProcessorMock()
    {
        return $this->getMockForAbstractClass(ProcessorInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RetryStrategyInterface
     */
    protected function getStrategyMock()
    {
        return $this->getMockForAbstractClass(RetryStrategyInterface::class);
    }
}
