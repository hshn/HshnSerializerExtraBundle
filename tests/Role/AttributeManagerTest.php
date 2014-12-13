<?php

namespace Hshn\SerializerExtraBundle\Role;

/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class AttributesManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AttributeManager
     */
    private $manager;

    protected function setUp()
    {
        $this->manager = new AttributeManager();
    }

    /**
     * @test
     */
    public function testAddAttributes()
    {
        $this->assertEquals([], $this->manager->getClasses());
        $this->manager->addAttributes('Foo', ['a', 'b']);

        $this->assertEquals(['Foo'], $this->manager->getClasses());
        $this->assertEquals(['a', 'b'], $this->manager->getAttributes('Foo'));

        $this->manager->addAttributes('Foo', ['b', 'c']);
        $this->assertEquals(['a', 'b', 'c'], $this->manager->getAttributes('Foo'));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function testThrowExceptionUnlessAddingAttributes()
    {
        $this->manager->getAttributes('Invalid Class');
    }
}
