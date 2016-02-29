<?php

namespace TreeHouse\Queue\Tests\Processor\Retry;

use Mockery as Mock;
use Mockery\MockInterface;
use TreeHouse\Queue\Amqp\EnvelopeInterface;
use TreeHouse\Queue\Message\Message;
use TreeHouse\Queue\Message\MessageProperties;
use TreeHouse\Queue\Message\Publisher\MessagePublisherInterface;
use TreeHouse\Queue\Processor\Retry\BackoffStrategy;
use TreeHouse\Queue\Processor\Retry\RetryProcessor;

class BackoffStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_can_retry_a_message()
    {
        $id = 1234;
        $body = 'test';
        $routingKey = 'foo';
        $priority = 3;
        $headers = ['foo' => 'bar'];

        /** @var MockInterface|EnvelopeInterface $envelope */
        $envelope = Mock::mock(EnvelopeInterface::class);
        $envelope->shouldReceive('getDeliveryTag')->andReturn($id);
        $envelope->shouldReceive('getBody')->andReturn($body);
        $envelope->shouldReceive('getRoutingKey')->andReturn($routingKey);
        $envelope->shouldReceive('getPriority')->andReturn($priority);
        $envelope->shouldReceive('getHeaders')->andReturn($headers);
        $envelope->shouldReceive('getContentType')->andReturn(MessageProperties::CONTENT_TYPE_BASIC);
        $envelope->shouldReceive('getDeliveryMode')->andReturn(MessageProperties::DELIVERY_MODE_PERSISTENT);

        $attempt = 2;
        $cooldownTime = 60;
        $cooldownDate = \DateTime::createFromFormat('U', time() + ($attempt * $cooldownTime));

        $publisher = $this->createPublisherMock();
        $publisher
            ->shouldReceive('publish')
            ->once()
            ->with(
                Mock::on(function (Message $retryMessage) use ($envelope, $attempt) {
                    $this->assertSame(
                        $envelope->getDeliveryTag(),
                        $retryMessage->getId(),
                        'Delivery tag of the retry-message is not the same'
                    );

                    $this->assertSame(
                        $envelope->getBody(),
                        $retryMessage->getBody(),
                        'Body of the retry-message is not the same'
                    );

                    $this->assertSame(
                        $envelope->getRoutingKey(),
                        $retryMessage->getRoutingKey(),
                        'Routing key of the retry-message is not the same'
                    );

                    $this->assertArraySubset(
                        $envelope->getHeaders(),
                        $retryMessage->getHeaders(),
                        'Headers are not properly cloned'
                    );

                    $this->assertSame(
                        $attempt,
                        $retryMessage->getHeader(RetryProcessor::PROPERTY_KEY),
                        'There should be an "attempt" header in the retry message'
                    );

                    return true;
                }),
                equalTo($cooldownDate)
            )
            ->andReturn(true)
        ;

        $strategy = new BackoffStrategy($publisher, $cooldownTime);
        $result = $strategy->retry($envelope, $attempt);

        $this->assertTrue($result);
    }

    /**
     * @return MockInterface|MessagePublisherInterface
     */
    private function createPublisherMock()
    {
        return Mock::mock(MessagePublisherInterface::class);
    }
}
