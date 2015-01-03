<?php


namespace Hshn\SerializerExtraBundle\VichUploader\UriResolver;



/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class ApplyFilterResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ApplyFilterResolver
     */
    private $resolver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $storage;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $cacheManager;

    protected function setUp()
    {
        $this->resolver = new ApplyFilterResolver(
            $this->storage = $this->getMock('Vich\UploaderBundle\Storage\StorageInterface'),
            $this->cacheManager = $this->getMockBuilder('Liip\ImagineBundle\Imagine\Cache\CacheManager')->disableOriginalConstructor()->getMock()
        );
    }

    /**
     * @test
     * @expectedException \Hshn\SerializerExtraBundle\VichUploader\UriResolver\ResolvingUriFailedException
     */
    public function testThrowExceptionUnlessFileHasFilter()
    {
        $file = $this->getFile(null);

        $this->resolver->resolve(new \stdClass(), $file);
    }

    /**
     * @test
     * @expectedException \Hshn\SerializerExtraBundle\VichUploader\UriResolver\ResolvingUriFailedException
     */
    public function testThrowExceptionUnlessResolveFilePath()
    {
        $file = $this->getFileWithProperty('foo', 'bar');

        $this
            ->storage
            ->expects($this->once())
            ->method('resolvePath')
            ->will($this->returnValue(null));

        $this->resolver->resolve(new \stdClass(), $file);
    }

    /**
     * @test
     */
    public function testResolve()
    {
        $file = $this->getFileWithProperty($filter = 'bar', $property = 'foo');

        $this
            ->storage
            ->expects($this->once())
            ->method('resolvePath')
            ->with($object = new \stdClass(), $property, $class = 'ClassName')
            ->will($this->returnValue($actualPath = '/path/to/file'));

        $this
            ->cacheManager
            ->expects($this->once())
            ->method('getBrowserPath')
            ->with($actualPath, $filter)
            ->will($this->returnValue($actualUri = 'http://foo/bar'));

        $this->resolver->resolve($object, $file, $class);
    }

    /**
     * @param $filter
     * @param $property
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getFileWithProperty($filter, $property)
    {
        $file = $this->getFile($filter);

        $file
            ->expects($this->atLeastOnce())
            ->method('getProperty')
            ->will($this->returnValue($property));

        return $file;
    }

    /**
     * @param $filter
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getFile($filter)
    {
        $file = $this->getMockBuilder('Hshn\SerializerExtraBundle\VichUploader\Configuration\File')->disableOriginalConstructor()->getMock();

        $file
            ->expects($this->atLeastOnce())
            ->method('getFilter')
            ->will($this->returnValue($filter));

        return $file;
    }

}
