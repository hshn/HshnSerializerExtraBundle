<?php

namespace Hshn\SerializerExtraBundle\VichUploader\UriResolver;


/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class StorageResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StorageResolver
     */
    private $resolver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $storage;

    protected function setUp()
    {
        $this->resolver = new StorageResolver(
            $this->storage = $this->getMock('Vich\UploaderBundle\Storage\StorageInterface')
        );
    }

    /**
     * @test
     * @expectedException \Hshn\SerializerExtraBundle\VichUploader\UriResolver\ResolvingUriFailedException
     */
    public function testThrowExceptionUnlessFileExists()
    {
        $this
            ->storage
            ->expects($this->once())
            ->method('resolveUri')
            ->with($object = new \stdClass(), $property = 'foo', $class = 'Boo')
            ->will($this->returnValue(null));

        $file = $this->getFile('foo');

        $this->resolver->resolve($object, $file, $class);
    }

    /**
     * @test
     */
    public function testResolve()
    {
        $this
            ->storage
            ->expects($this->once())
            ->method('resolveUri')
            ->with($object = new \stdClass(), $property = 'foo', $class = 'Boo')
            ->will($this->returnValue($uri = 'http://foo/bar'));

        $file = $this->getFile('foo');

        $this->assertEquals($uri, $this->resolver->resolve($object, $file, $class));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getFile($property)
    {
        $file =  $this->getMockBuilder('Hshn\SerializerExtraBundle\VichUploader\Configuration\File')->disableOriginalConstructor()->getMock();

        $file
            ->expects($this->atLeastOnce())
            ->method('getProperty')
            ->will($this->returnValue($property));

        return $file;
    }
}
