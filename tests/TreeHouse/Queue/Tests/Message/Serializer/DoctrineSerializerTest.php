<?php

namespace TreeHouse\Queue\Tests\Message\Serializer;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use TreeHouse\Queue\Message\Serializer\DoctrineSerializer;
use TreeHouse\Queue\Tests\Mock\ObjectMock;

class DoctrineSerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @varDoctrineSerializer
     */
    protected $doctrine;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $meta = $this->getMockBuilder(ClassMetadata::class)->getMock();
        $meta->expects($this->any())
             ->method('getIdentifierValues')
             ->will($this->returnCallback(
                 function (ObjectMock $value) {
                     return ['id' => $value->getId()];
                 }
             )
             );

        $manager = $this->getMockBuilder(ObjectManager::class)->getMock();
        $manager->expects($this->any())->method('getClassMetadata')->will($this->returnValue($meta));

        $this->doctrine = $this->getMockBuilder(ManagerRegistry::class)->getMock();
        $this->doctrine
            ->expects($this->any())
            ->method('getManager')
            ->will($this->returnValue($manager))
        ;
    }

    /**
     * @test
     */
    public function it_can_be_constructed()
    {
        $serializer = new DoctrineSerializer($this->doctrine);

        $this->assertInstanceOf(DoctrineSerializer::class, $serializer);
    }

    /**
     * @test
     */
    public function it_can_serialize_messages()
    {
        $object = new ObjectMock(1234);

        $serializer = new DoctrineSerializer($this->doctrine);
        $this->assertEquals('{"id":1234}', $serializer->serialize($object));
    }

    /**
     * @test
     * @dataProvider getInvalidTestData
     * @expectedException \InvalidArgumentException
     *
     * @param mixed $value
     */
    public function it_cannot_serialize_with_invalid_arguments($value)
    {
        $serializer = new DoctrineSerializer($this->doctrine);
        $serializer->serialize($value);
    }

    /**
     * @return array
     */
    public function getInvalidTestData()
    {
        return [
            [1234],
            ['1234'],
            [[1234]],
        ];
    }
}
