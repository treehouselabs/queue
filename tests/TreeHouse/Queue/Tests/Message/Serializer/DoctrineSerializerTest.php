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

    public function testConstructor()
    {
        $serializer = new DoctrineSerializer($this->doctrine);

        $this->assertInstanceOf(DoctrineSerializer::class, $serializer);
    }

    public function testSerialize()
    {
        $object = new ObjectMock(1234);

        $serializer = new DoctrineSerializer($this->doctrine);
        $this->assertEquals('{"id":1234}', $serializer->serialize($object));
    }

    /**
     * @dataProvider getInvalidTestData
     * @expectedException \InvalidArgumentException
     *
     * @param mixed $value
     */
    public function testInvalidArgument($value)
    {
        $serializer = new DoctrineSerializer($this->doctrine);
        $serializer->serialize($value);
    }

    public function getInvalidTestData()
    {
        return [
            [1234],
            ['1234'],
            [[1234]],
        ];
    }

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
}
