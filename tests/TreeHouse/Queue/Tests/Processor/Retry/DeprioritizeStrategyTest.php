<?php

namespace TreeHouse\Queue\Tests\Processor\Retry;

use TreeHouse\Queue\Message\Message;
use TreeHouse\Queue\Message\Publisher\MessagePublisherInterface;
use TreeHouse\Queue\Processor\Retry\DeprioritizeStrategy;
use TreeHouse\Queue\Processor\Retry\RetryProcessor;

class DeprioritizeStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testRetry()
    {
        $attempt    = 2;
        $body       = 'test';
        $routingKey = 'foo';
        $priority   = 3;

        $message = new Message($body);
        $message->setRoutingKey($routingKey);
        $message->setPriority($priority);
        $message->getProperties()->set('foo', 'bar');

        $publisher = $this->createPublisherMock();
        $publisher
            ->expects($this->once())
            ->method('publish')
            ->with(
                $this->callback(function (Message $retryMessage) use ($message, $attempt) {
                    $this->assertSame(
                        $message->getBody(),
                        $retryMessage->getBody(),
                        'Body of the retry-message is not the same'
                    );

                    $this->assertSame(
                        $message->getRoutingKey(),
                        $retryMessage->getRoutingKey(),
                        'Routing key of the retry-message is not the same'
                    );

                    $this->assertSame(
                        $message->getProperties()->get('foo'),
                        $retryMessage->getProperties()->get('foo'),
                        'Properties are not properly cloned'
                    );

                    $this->assertSame(
                        $retryMessage->getPriority(),
                        $message->getPriority() - 1,
                        'Priority should decrease with 1'
                    );

                    $this->assertSame(
                        $attempt,
                        $retryMessage->getProperties()->get(RetryProcessor::PROPERTY_KEY),
                        'There should be an "attempt" key in the retry message\'s properties'
                    );

                    return true;
                })
            )
            ->will($this->returnValue(true))
        ;

        $strategy = new DeprioritizeStrategy($publisher);
        $result = $strategy->retry($message, $attempt);

        $this->assertTrue($result);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|MessagePublisherInterface
     */
    protected function createPublisherMock()
    {
        return $this->getMockBuilder(MessagePublisherInterface::class)->getMockForAbstractClass();
    }
}
