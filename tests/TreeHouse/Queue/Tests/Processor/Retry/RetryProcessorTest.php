<?php

namespace TreeHouse\Queue\Tests\Processor\Retry;

use Mockery as Mock;
use Mockery\MockInterface;
use TreeHouse\Queue\Amqp\EnvelopeInterface;
use TreeHouse\Queue\Processor\ProcessorInterface;
use TreeHouse\Queue\Processor\Retry\RetryProcessor;
use TreeHouse\Queue\Processor\Retry\RetryStrategyInterface;

class RetryProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $processor = new RetryProcessor($this->createProcessorMock(), $this->createStrategyMock());

        $this->assertInstanceOf(RetryProcessor::class, $processor);
    }

    /**
     * @test
     */
    public function it_can_get_and_set()
    {
        $processor = new RetryProcessor($this->createProcessorMock(), $this->createStrategyMock());

        $processor->setMaxAttempts(5);
        $this->assertEquals(5, $processor->getMaxAttempts());

        $processor2 = new RetryProcessor($this->createProcessorMock(), $this->createStrategyMock());
        $processor->setProcessor($processor2);
        $this->assertSame($processor2, $processor->getProcessor());
    }

    /**
     * @test
     */
    public function it_can_process_a_message()
    {
        $inner = $this->createProcessorMock();
        $strategy = $this->createStrategyMock();

        $processor = new RetryProcessor($inner, $strategy);
        $inner->shouldReceive('process')->once()->andReturn(true);
        $strategy->shouldReceive('retry')->never();

        $envelope = $this->createEnvelopeMock();
        $processor->process($envelope);
    }

    /**
     * @test
     */
    public function it_retries_when_processor_throws_exception()
    {
        $inner = $this->createProcessorMock();
        $strategy = $this->createStrategyMock();

        $exception = new \Exception();
        $processor = new RetryProcessor($inner, $strategy);

        $strategy
            ->shouldReceive('retry')
            ->once()
            ->with(any(EnvelopeInterface::class), 2, $exception)
            ->andReturn(true)
        ;
        $inner->shouldReceive('process')->once()->andThrow($exception);

        $envelope = $this->createEnvelopeMock(1);
        $envelope->shouldReceive('getDeliveryTag')->andReturnNull();

        $result = $processor->process($envelope);

        $this->assertTrue($result, 'The ->process() method should return the value from the strategy');
    }

    /**
     * @test
     * @expectedException \TreeHouse\Queue\Exception\ProcessExhaustedException
     */
    public function it_cannot_exceed_max_retries()
    {
        $inner = $this->createProcessorMock();
        $strategy = $this->createStrategyMock();

        $inner->shouldReceive('process')->once()->andThrow(new \Exception());

        // create message for second attempt
        $envelope = $this->createEnvelopeMock(2);
        $envelope->shouldReceive('getDeliveryTag')->andReturnNull();

        $processor = new RetryProcessor($inner, $strategy);
        $processor->setMaxAttempts(2);
        $processor->process($envelope);
    }

    /**
     * @return MockInterface|ProcessorInterface
     */
    private function createProcessorMock()
    {
        return Mock::mock(ProcessorInterface::class);
    }

    /**
     * @return MockInterface|RetryStrategyInterface
     */
    private function createStrategyMock()
    {
        return Mock::mock(RetryStrategyInterface::class);
    }

    /**
     * @param int|bool $attempt
     *
     * @return MockInterface|EnvelopeInterface
     */
    private function createEnvelopeMock($attempt = false)
    {
        $mock = Mock::mock(EnvelopeInterface::class);
        $mock
            ->shouldReceive('getHeader')
            ->with(RetryProcessor::PROPERTY_KEY)
            ->andReturn($attempt)
        ;

        return $mock;
    }
}
