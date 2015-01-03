<?php


namespace Hshn\SerializerExtraBundle\VichUploader\UriResolver;



/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class ChainResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ChainResolver
     */
    private $resolver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $primary;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $secondary;

    protected function setUp()
    {
        $this->resolver = new ChainResolver([
            $this->primary = $this->getMock('Hshn\SerializerExtraBundle\VichUploader\UriResolverInterface'),
            $this->secondary = $this->getMock('Hshn\SerializerExtraBundle\VichUploader\UriResolverInterface')
        ]);
    }

    /**
     * @test
     * @expectedException \Hshn\SerializerExtraBundle\VichUploader\UriResolverException
     */
    public function testThrowExceptionUnlessResolveUri()
    {
        $object = new \stdClass();
        $file = $this->getFile();
        $class = 'Foo';

        $this
            ->primary
            ->expects($this->once())
            ->method('resolve')
            ->willThrowException($this->getException());

        $this
            ->secondary
            ->expects($this->once())
            ->method('resolve')
            ->willThrowException($this->getException());

        $this->resolver->resolve($object, $file, $class);
    }

    /**
     * @test
     */
    public function testResolveByPrimary()
    {
        $object = new \stdClass();
        $file = $this->getFile();
        $class = 'Foo';

        $this
            ->primary
            ->expects($this->once())
            ->method('resolve')
            ->willReturn($uri = 'http://foo/bar');

        $this
            ->secondary
            ->expects($this->never())
            ->method('resolve');

        $this->assertEquals($uri, $this->resolver->resolve($object, $file, $class));
    }

    /**
     * @test
     */
    public function testResolveBySecondary()
    {
        $object = new \stdClass();
        $file = $this->getFile();
        $class = 'Foo';

        $this
            ->primary
            ->expects($this->once())
            ->method('resolve')
            ->willThrowException($this->getException());

        $this
            ->secondary
            ->expects($this->once())
            ->method('resolve')
            ->willReturn($uri = 'http://foo/bar');

        $this->assertEquals($uri, $this->resolver->resolve($object, $file, $class));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getException()
    {
        return $this->getMock('Hshn\SerializerExtraBundle\VichUploader\UriResolverException');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getFile()
    {
        return $this->getMockBuilder('Hshn\SerializerExtraBundle\VichUploader\Configuration\File')->disableOriginalConstructor()->getMock();
    }
}
