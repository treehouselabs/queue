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

    /**
     * @dataProvider getTestData
     *
     * @param mixed  $value
     * @param string $expected
     */
    public function testSerialize($value, $expected)
    {
        $serializer = new DoctrineSerializer($this->doctrine);
        $this->assertEquals($expected, $serializer->serialize($value));
    }

    public function getTestData()
    {
        return [
            [2, "[2]"],   // assume integers are identifiers
            ["2", "[2]"], // cast numeric values to integers
            [new ObjectMock(1234), "[1234]"]
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
