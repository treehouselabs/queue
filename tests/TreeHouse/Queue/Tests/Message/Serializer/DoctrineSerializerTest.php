<?php

namespace TreeHouse\Queue\Tests\Message\Serializer;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use TreeHouse\Queue\Message\Serializer\DoctrineSerializer;

class DoctrineSerializerTest extends \PHPUnit_Framework_TestCase
{
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
            [2, "[2]"], // assume integers are identifiers
            [new EntityMock(1234), "[1234]"]
        ];
    }

    protected function setUp()
    {
        $meta = $this->getMockBuilder(ClassMetadata::class)->getMock();
        $meta->expects($this->any())->method('getIdentifierValues')->will($this->returnCallback(function ($value) {
            /** @var EntityMock $value */

            return ['id' => $value->getId()];
        }));

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

class EntityMock
{
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }
}
