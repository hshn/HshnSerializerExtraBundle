<?php

namespace Hshn\SerializerExtraBundle\Authority;

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
        $this->repository = new ConfigurationRepository();
    }

    /**
     * @test
     */
    public function testAddAttributes()
    {
        $this->assertEquals([], $this->repository->getClasses());
        $this->repository->set('Foo', $config = new Configuration());

        $this->assertEquals(['Foo'], $this->repository->getClasses());
        $this->assertSame($config, $this->repository->get('Foo'));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function testThrowExceptionUnlessAddingAttributes()
    {
        $this->repository->get('Invalid Class');
    }
}
