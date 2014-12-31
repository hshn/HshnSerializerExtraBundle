<?php


namespace Hshn\SerializerExtraBundle;


/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class ParameterBagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ParameterBag
     */
    private $parameterBag;

    protected function setUp()
    {
        $this->parameterBag = new ParameterBag([
            'foo' => 'bar',
        ]);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function testThrowExceptionUnlessGetWithValidKey()
    {
        $this->parameterBag->get('invalid key');
    }

    /**
     * @test
     */
    public function test()
    {
        $this->assertEquals('bar', $this->parameterBag->get('foo'));
        $this->assertEquals(['foo'], $this->parameterBag->keys());
        $this->assertEquals(['foo' => 'bar'], $this->parameterBag->all());

        $this->parameterBag->set('foo', 'bazz');
        $this->assertEquals('bazz', $this->parameterBag->get('foo'));
        $this->assertEquals(['foo'], $this->parameterBag->keys());
        $this->assertEquals(['foo' => 'bazz'], $this->parameterBag->all());

        $this->parameterBag->set('bar', 'foo');
        $this->assertEquals('bazz', $this->parameterBag->get('foo'));
        $this->assertEquals('foo', $this->parameterBag->get('bar'));
        $this->assertEquals(['foo', 'bar'], $this->parameterBag->keys());
        $this->assertEquals(['foo' => 'bazz', 'bar' => 'foo'], $this->parameterBag->all());
    }
}
