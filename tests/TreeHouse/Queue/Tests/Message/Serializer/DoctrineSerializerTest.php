<?php

namespace TreeHouse\Queue\Tests\Message\Serializer;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Mockery as Mock;
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
        $meta = Mock::mock(ClassMetadata::class);
        $meta->shouldReceive('getIdentifierValues')
            ->andReturnUsing(function (ObjectMock $value) {
                return ['id' => $value->getId()];
            })
        ;

        $manager = Mock::mock(ObjectManager::class);
        $manager->shouldReceive('getClassMetadata')->andReturn($meta);

        $this->doctrine = Mock::mock(ManagerRegistry::class);
        $this->doctrine->shouldReceive('getManager')->andReturn($manager);
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
