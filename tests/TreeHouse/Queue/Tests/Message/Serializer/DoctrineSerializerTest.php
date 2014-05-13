<?php

namespace TreeHouse\Queue\Tests\Message\Serializer;

use Doctrine\Common\Persistence\ManagerRegistry;
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
            [2, 2], // assume integers are identifiers
            // TODO test with actual entity
        ];
    }

    protected function setUp()
    {
        $this->doctrine = $this
            ->getMockBuilder(ManagerRegistry::class)
            ->getMock()
        ;
    }
}
