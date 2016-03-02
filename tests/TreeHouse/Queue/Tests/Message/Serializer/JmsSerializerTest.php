<?php

namespace TreeHouse\Queue\Tests\Message\Serializer;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Mockery as Mock;
use Mockery\MockInterface;
use TreeHouse\Queue\Message\Serializer\JmsSerializer;

class JmsSerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MockInterface|SerializerInterface
     */
    protected $jmsSerializer;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->jmsSerializer = Mock::mock(SerializerInterface::class);
    }

    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $serializer = new JmsSerializer($this->jmsSerializer);

        $this->assertInstanceOf(JmsSerializer::class, $serializer);
    }

    /**
     * @test
     */
    public function it_can_serialize()
    {
        $this->jmsSerializer->shouldReceive('serialize')->once();

        $serializer = new JmsSerializer($this->jmsSerializer);
        $serializer->serialize([1234]);
    }

    /**
     * @test
     */
    public function it_can_serialize_with_context_and_format()
    {
        $format = 'yml';
        $context = Mock::mock(SerializationContext::class);

        $this->jmsSerializer
            ->shouldReceive('serialize')
            ->once()
            ->with([1234], $format, $context)
        ;

        $serializer = new JmsSerializer($this->jmsSerializer, $format, $context);
        $serializer->serialize([1234]);
    }
}
