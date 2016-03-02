<?php

namespace TreeHouse\Queue\Tests\Amqp\Driver;

use TreeHouse\Queue\Amqp\EnvelopeInterface;
use TreeHouse\Queue\Amqp\ExchangeInterface;
use TreeHouse\Queue\Amqp\QueueInterface;
use TreeHouse\Queue\Message\MessageProperties;

abstract class AbstractDriverEnvelopeTest extends \PHPUnit_Framework_TestCase
{
    use DriverTestTrait;

    /**
     * @test
     * @functional
     */
    public function it_can_be_created()
    {
        $body = 'foobar';
        $messageId = 'abc123';
        $routingKey = 'baz';
        $priority = 123;
        $contentType = MessageProperties::CONTENT_TYPE_BASIC;
        $contentEncoding = 'utf8';
        $userId = 'guest';
        $appId = 'testapp';
        $deliveryMode = MessageProperties::DELIVERY_MODE_NON_PERSISTENT;
        $timestamp = time();
        $expiration = strval(time() + 600);
        $correlationId = '12345678';
        $type = 'type1';
        $replyTo = 'foo@example.org';
        $headers = [
            'x-foo' => 'bar',
        ];

        $properties = [
            'content_type' => $contentType,
            'content_encoding' => $contentEncoding,
            'message_id' => $messageId,
            'user_id' => $userId,
            'app_id' => $appId,
            'delivery_mode' => $deliveryMode,
            'priority' => $priority,
            'timestamp' => $timestamp,
            'expiration' => $expiration,
            'type' => $type,
            'reply_to' => $replyTo,
            'headers' => $headers,
            'correlation_id' => $correlationId,
        ];

        $exchange = $this->getExchange();
        $queue = $this->getQueue();
        $queue->bind($exchange->getName(), $routingKey);

        $exchange->publish($body, $routingKey, null, $properties);
        $envelope = $queue->get();

        $this->assertInstanceOf(EnvelopeInterface::class, $envelope);
        $this->assertSame($appId, $envelope->getAppId());
        $this->assertSame($body, $envelope->getBody());
        $this->assertSame($contentEncoding, $envelope->getContentEncoding());
        $this->assertSame($contentType, $envelope->getContentType());
        $this->assertSame($correlationId, $envelope->getCorrelationId());
        $this->assertSame($deliveryMode, $envelope->getDeliveryMode());
        $this->assertSame(1, $envelope->getDeliveryTag());
        $this->assertSame($exchange->getName(), $envelope->getExchangeName());
        $this->assertSame($expiration, $envelope->getExpiration());
        $this->assertSame($headers, $envelope->getHeaders());
        $this->assertSame($headers['x-foo'], $envelope->getHeader('x-foo'));
        $this->assertSame($messageId, $envelope->getMessageId());
        $this->assertSame($priority, $envelope->getPriority());
        $this->assertSame($replyTo, $envelope->getReplyTo());
        $this->assertSame($routingKey, $envelope->getRoutingKey());
        $this->assertSame($timestamp, $envelope->getTimestamp());
        $this->assertSame($type, $envelope->getType());
        $this->assertSame($userId, $envelope->getUserId());
    }

    /**
     * @return ExchangeInterface
     */
    abstract protected function getExchange();

    /**
     * @return QueueInterface
     */
    abstract protected function getQueue();
}
