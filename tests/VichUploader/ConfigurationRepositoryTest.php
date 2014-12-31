<?php


namespace Hshn\SerializerExtraBundle\VichUploader;



/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class ConfigurationRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConfigurationRepository
     */
    private $repository;

    protected function setUp()
    {
        $this->repository = new ConfigurationRepository([
            $this->getConfiguration('Foo'),
        ]);
    }

    /**
     * @test
     */
    public function testConstruct()
    {
        $this->assertInstanceOf('Hshn\SerializerExtraBundle\VichUploader\Configuration', $this->repository->get('Foo'));
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function testThrowExceptionUnlessGetWithInvalidClass()
    {
        $this->repository->get('InvalidClass');
    }

    /**
     * @test
     */
    public function testAll()
    {
        $all = $this->repository->all();
        $this->assertCount(1, $all);
    }

    /**
     * @test
     */
    public function testAdd()
    {
        $this->repository->add($configuration = $this->getConfiguration('Bar'));

        $all = $this->repository->all();
        $this->assertCount(2, $all);

        $this->assertSame($configuration, $this->repository->get('Bar'));
        $this->assertNotSame($configuration, $this->repository->get('Foo'));
    }

    /**
     * @param $class
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getConfiguration($class)
    {
        $configuration = $this->getMockBuilder('Hshn\SerializerExtraBundle\VichUploader\Configuration')->disableOriginalConstructor()->getMock();

        $configuration
            ->expects($this->atLeastOnce())
            ->method('getClass')
            ->will($this->returnValue($class));

        return $configuration;
    }
}
