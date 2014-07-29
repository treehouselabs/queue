<?php

namespace TreeHouse\Queue\Tests\Message\Serializer;

use JMS\Serializer\Exclusion\GroupsExclusionStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use TreeHouse\Queue\Message\Serializer\JmsSerializer;

class JmsSerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|SerializerInterface
     */
    protected $jmsSerializer;

    public function testConstructor()
    {
        $serializer = new JmsSerializer($this->jmsSerializer);

        $this->assertInstanceOf(JmsSerializer::class, $serializer);
    }

    public function testSerialize()
    {
        $this->jmsSerializer->expects($this->once())->method('serialize');

        $serializer = new JmsSerializer($this->jmsSerializer);
        $serializer->serialize([1234]);
    }

    public function testFormatAndGroups()
    {
        $format = 'yml';
        $groups = ['foo'];

        $this->jmsSerializer
            ->expects($this->once())
            ->method('serialize')
            ->with(
                $this->equalTo([1234]),
                $this->equalTo($format),
                $this->callback(function (SerializationContext $context) use ($groups) {
                    return $context->getExclusionStrategy() instanceof GroupsExclusionStrategy;
                })
            )
        ;

        $serializer = new JmsSerializer($this->jmsSerializer, $groups, $format);
        $serializer->serialize([1234]);
    }

    protected function setUp()
    {
        $this->jmsSerializer = $this->getMockBuilder(SerializerInterface::class)->getMockForAbstractClass();
    }
}
