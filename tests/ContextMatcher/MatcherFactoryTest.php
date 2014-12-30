<?php


namespace Hshn\SerializerExtraBundle\ContextMatcher;



/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class MatcherFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MatcherFactory
     */
    private $factory;

    protected function setUp()
    {
        $this->factory = new MatcherFactory();
    }

    /**
     * @tesst
     */
    public function testDepth()
    {
        $this->assertInstanceOf('Hshn\SerializerExtraBundle\ContextMatcher\MatcherInterface', $this->factory->depth(1));
    }
}
