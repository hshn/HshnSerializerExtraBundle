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
    private $originalResolver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $cacheManager;

    protected function setUp()
    {
        $this->resolver = new ApplyFilterResolver(
            $this->originalResolver = $this->getMock('Hshn\SerializerExtraBundle\VichUploader\UriResolverInterface'),
            $this->cacheManager = $this->getMockBuilder('Liip\ImagineBundle\Imagine\Cache\CacheManager')->disableOriginalConstructor()->getMock()
        );
    }

    /**
     * @test
     */
    public function testDoNotFilterUnlessFileHasFilter()
    {
        $file = $this->getFile(null);

        $this
            ->originalResolver
            ->expects($this->once())
            ->method('resolve')
            ->with($object = new \stdClass(), $file, $class = 'Foo')
            ->willReturn($expectedUri = 'http://localhost/foo/bar');

        $this->assertEquals($expectedUri, $this->resolver->resolve($object, $file, $class));
    }

    /**
     * @test
     */
    public function testResolve()
    {
        $file = $this->getFile($filter = 'bar');

        $this
            ->originalResolver
            ->expects($this->once())
            ->method('resolve')
            ->with($object = new \stdClass(), $file, $class = 'ClassName')
            ->willReturn($actualPath = '/path/to/file');

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
