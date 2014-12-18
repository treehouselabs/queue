<?php

namespace TreeHouse\Queue\Tests\Processor\Retry;

use TreeHouse\Queue\Message\Message;
use TreeHouse\Queue\Message\Publisher\MessagePublisherInterface;
use TreeHouse\Queue\Processor\Retry\BackoffStrategy;
use TreeHouse\Queue\Processor\Retry\RetryProcessor;

class BackoffStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testRetry()
    {
        $message = new Message('test');
        $message->setRoutingKey('foo');
        $message->getProperties()->set('foo', 'bar');

        $attempt      = 2;
        $cooldownTime = 60;
        $cooldownDate = \DateTime::createFromFormat('U', time() + ($attempt * $cooldownTime));

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

                    $this->assertEquals(
                        $attempt,
                        $retryMessage->getProperties()->get(RetryProcessor::PROPERTY_KEY),
                        'There should be an "attempt" key in the retry message\'s properties'
                    );

                    return true;
                }),
                $cooldownDate
            )
            ->will($this->returnValue(true))
        ;

        $strategy = new BackoffStrategy($publisher, $cooldownTime);
        $result   = $strategy->retry($message, $attempt);

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
