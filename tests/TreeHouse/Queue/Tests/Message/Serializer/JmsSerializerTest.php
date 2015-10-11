<?php

namespace TreeHouse\Queue\Tests\Message\Serializer;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use TreeHouse\Queue\Message\Serializer\JmsSerializer;

class JmsSerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|SerializerInterface
     */
    protected $jmsSerializer;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->jmsSerializer = $this->getMockBuilder(SerializerInterface::class)->getMockForAbstractClass();
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
        $this->jmsSerializer->expects($this->once())->method('serialize');

        $serializer = new JmsSerializer($this->jmsSerializer);
        $serializer->serialize([1234]);
    }

    /**
     * @test
     */
    public function it_can_serialize_with_context_and_format()
    {
        $format = 'yml';
        $context = SerializationContext::create()->setGroups(['foo']);

        $this->jmsSerializer
            ->expects($this->once())
            ->method('serialize')
            ->with(
                $this->equalTo([1234]),
                $this->equalTo($format),
                $this->identicalTo($context)
            )
        ;

        $serializer = new JmsSerializer($this->jmsSerializer, $context, $format);
        $serializer->serialize([1234]);
    }
}
