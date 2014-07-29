<?php

namespace TreeHouse\Queue\Tests\Message\Composer;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectRepository;
use TreeHouse\Queue\Message\Composer\DoctrineMessageComposer;
use TreeHouse\Queue\Message\Serializer\JsonSerializer;
use TreeHouse\Queue\Tests\Mock\ObjectMock;

class DoctrineMessageComposerTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $doctrine = $this->getDoctrineMock();
        $composer = new DoctrineMessageComposer($doctrine, new JsonSerializer(), ObjectMock::class);
        $this->assertInstanceOf(DoctrineMessageComposer::class, $composer);

        return $composer;
    }

    public function testCompose()
    {
        $doctrine = $this->getDoctrineMock();
        $composer = new DoctrineMessageComposer($doctrine, new JsonSerializer(), ObjectMock::class);

        $message = $composer->compose(1234);
        $this->assertEquals('[1234]', $message->getBody(), 'identifier is looked up');

        $message = $composer->compose(['5678']);
        $this->assertEquals('[5678]', $message->getBody(), 'arrays are supported, numeric values cast to integers');

        $message = $composer->compose(['id' => 3456]);
        $this->assertEquals('[3456]', $message->getBody(), 'associative arrays are passed directly to Doctrine');

        $message = $composer->compose(new ObjectMock(6789));
        $this->assertEquals('[6789]', $message->getBody(), 'existing object is not converted');
    }

    /**
     * @dataProvider      getInvalidArguments
     * @expectedException \RuntimeException
     */
    public function testInvalidArguments($arg)
    {
        $doctrine = $this->getDoctrineMock();
        $composer = new DoctrineMessageComposer($doctrine, new JsonSerializer(), ObjectMock::class);

        $composer->compose($arg);
    }

    public function getInvalidArguments()
    {
        return [
            [new \stdClass],
            [false],
            [null],
        ];
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ManagerRegistry
     */
    private function getDoctrineMock()
    {
        $repo = $this->getMockBuilder(ObjectRepository::class)->getMockForAbstractClass();
        $repo->expects($this->any())
            ->method('find')
            ->will($this->returnCallback(function ($value) {
                if (is_array($value) && is_numeric(current($value))) {
                    $value = current($value);
                }

                return is_numeric($value) ? new ObjectMock(intval($value)) : null;
            }))
        ;

        $doctrine = $this->getMockBuilder(ManagerRegistry::class)->getMockForAbstractClass();
        $doctrine->expects($this->any())->method('getRepository')->will($this->returnValue($repo));

        return $doctrine;
    }
}
