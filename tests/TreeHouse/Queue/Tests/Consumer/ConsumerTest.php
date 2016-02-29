<?php

namespace TreeHouse\Queue\Tests\Consumer;

use Mockery as Mock;
use Mockery\MockInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use TreeHouse\Queue\Amqp\EnvelopeInterface;
use TreeHouse\Queue\Amqp\QueueInterface;
use TreeHouse\Queue\Consumer\Consumer;
use TreeHouse\Queue\Consumer\ConsumerInterface;
use TreeHouse\Queue\Event\ConsumeEvent;
use TreeHouse\Queue\Event\ConsumeExceptionEvent;
use TreeHouse\Queue\Processor\ProcessorInterface;
use TreeHouse\Queue\QueueEvents;

class ConsumerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MockInterface|QueueInterface
     */
    protected $queue;

    /**
     * @var MockInterface|ProcessorInterface
     */
    protected $processor;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->queue = Mock::mock(QueueInterface::class);
        $this->processor = Mock::mock(ProcessorInterface::class);
    }

    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $consumer = new Consumer($this->queue, $this->processor);

        $this->assertInstanceOf(ConsumerInterface::class, $consumer);
    }

    /**
     * @test
     */
    public function it_can_return_its_event_dispatcher()
    {
        $dispatcher = new EventDispatcher();
        $consumer = new Consumer($this->queue, $this->processor, $dispatcher);

        $this->assertSame($dispatcher, $consumer->getEventDispatcher());
    }

    /**
     * @test
     */
    public function it_can_get_messages()
    {
        $envelope = Mock::mock(EnvelopeInterface::class);

        $this->queue->shouldReceive('get')->twice()->andReturnValues([$envelope, false]);

        $consumer = new Consumer($this->queue, $this->processor);
        $this->assertSame($envelope, $consumer->get());
        $this->assertNull($consumer->get());
    }

    /**
     * @test
     */
    public function it_can_consume_messages()
    {
        $tag = 'abc123';
        $flags = QueueInterface::AUTOACK;
        $result = true;

        /** @var MockInterface|EnvelopeInterface $envelope */
        $envelope = Mock::mock(EnvelopeInterface::class);
        $envelope->shouldReceive('getDeliveryTag')->andReturn($tag);

        /** @var MockInterface|EventDispatcherInterface $dispatcher */
        $dispatcher = Mock::mock(EventDispatcherInterface::class);

        // assert two events: before and after processing
        $dispatcher
            ->shouldReceive('dispatch')
            ->with(QueueEvents::CONSUME_MESSAGE, Mock::on(function (ConsumeEvent $event) use ($envelope) {
                $this->assertSame($envelope, $event->getEnvelope());

                return true;
            }))
        ;
        $dispatcher
            ->shouldReceive('dispatch')
            ->with(QueueEvents::CONSUMED_MESSAGE, Mock::on(function (ConsumeEvent $event) use ($result) {
                $this->assertSame($result, $event->getResult());

                return true;
            }))
        ;

        // assert that the queue is consumed and the callback is called
        $this->queue
            ->shouldReceive('consume')
            ->once()
            ->with(any(\Closure::class), $flags)
            ->andReturnUsing(function (callable $callback) use ($envelope) {
                return $callback($envelope);
            })
        ;

        // message should be ack-ed after processing
        $this->queue->shouldReceive('ack')->once()->with($tag);
        $this->processor->shouldReceive('process')->once()->with($envelope)->andReturn($result);

        $consumer = new Consumer($this->queue, $this->processor, $dispatcher);
        $consumer->consume($flags);
    }

    /**
     * @test
     * @expectedException \RuntimeException
     * @expectedExceptionMessage This is a test exception
     */
    public function it_can_handle_consume_exceptions()
    {
        $tag = 'abc123';
        $exception = new \RuntimeException('This is a test exception');

        /** @var MockInterface|EnvelopeInterface $envelope */
        $envelope = Mock::mock(EnvelopeInterface::class);
        $envelope->shouldReceive('getDeliveryTag')->andReturn($tag);

        /** @var MockInterface|EventDispatcherInterface $dispatcher */
        $dispatcher = Mock::mock(EventDispatcherInterface::class);
        $dispatcher->shouldReceive('dispatch')->with(QueueEvents::CONSUME_MESSAGE, any(ConsumeEvent::class));
        $dispatcher
            ->shouldReceive('dispatch')
            ->with(QueueEvents::CONSUME_EXCEPTION, Mock::on(function (ConsumeExceptionEvent $event) use ($exception) {
                $this->assertSame($exception, $event->getException());

                return true;
            }))
        ;

        // assert that the queue is consumed and the callback is called
        $this->queue
            ->shouldReceive('consume')
            ->once()
            ->andReturnUsing(function (callable $callback) use ($envelope) {
                return $callback($envelope);
            })
        ;

        // message should be ack-ed after processing
        $this->processor->shouldReceive('process')->once()->with($envelope)->andThrow($exception);
        $this->queue->shouldReceive('nack')->once()->with($tag, false);

        $consumer = new Consumer($this->queue, $this->processor, $dispatcher);
        $consumer->consume();
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function it_can_nack_and_requeue_failed_message()
    {
        $tag = 'abc123';

        /** @var MockInterface|EnvelopeInterface $envelope */
        $envelope = Mock::mock(EnvelopeInterface::class);
        $envelope->shouldReceive('getDeliveryTag')->andReturn($tag);

        // assert that the queue is consumed and the callback is called
        $this->queue
            ->shouldReceive('consume')
            ->once()
            ->andReturnUsing(function (callable $callback) use ($envelope) {
                return $callback($envelope);
            })
        ;

        // message should be ack-ed after processing
        $this->queue->shouldReceive('nack')->once()->with($tag, true);

        $this->processor
            ->shouldReceive('process')
            ->once()
            ->with($envelope)
            ->andThrow(new \RuntimeException())
        ;

        $consumer = new Consumer($this->queue, $this->processor);
        $consumer->setNackRequeue(true);
        $consumer->consume();
    }

    /**
     * @test
     */
    public function it_can_ack_messages()
    {
        $tag = 'abc123';

        /** @var MockInterface|EnvelopeInterface $envelope */
        $envelope = Mock::mock(EnvelopeInterface::class);
        $envelope->shouldReceive('getDeliveryTag')->andReturn($tag);
        $this->queue->shouldReceive('ack')->once()->with($tag);

        $consumer = new Consumer($this->queue, $this->processor);
        $consumer->ack($envelope);
    }

    /**
     * @test
     */
    public function it_can_nack_messages()
    {
        $tag = 'abc123';

        /** @var MockInterface|EnvelopeInterface $envelope */
        $envelope = Mock::mock(EnvelopeInterface::class);
        $envelope->shouldReceive('getDeliveryTag')->andReturn($tag);
        $this->queue->shouldReceive('nack')->once()->with($tag, null);

        $consumer = new Consumer($this->queue, $this->processor);
        $consumer->nack($envelope);
    }

    /**
     * @test
     */
    public function it_can_nack_and_requeue_a_message()
    {
        $tag = 'abc123';
        $requeue = true;

        /** @var MockInterface|EnvelopeInterface $envelope */
        $envelope = Mock::mock(EnvelopeInterface::class);
        $envelope->shouldReceive('getDeliveryTag')->andReturn($tag);
        $this->queue->shouldReceive('nack')->once()->with($tag, QueueInterface::REQUEUE);

        $consumer = new Consumer($this->queue, $this->processor);
        $consumer->nack($envelope, $requeue);
    }
}
