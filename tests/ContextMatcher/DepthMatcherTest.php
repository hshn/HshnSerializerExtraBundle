<?php


namespace Hshn\SerializerExtraBundle\ContextMatcher;



/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class DepthMatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testDepthUnlimited()
    {
        $matcher = new DepthMatcher(-1);
        $this->assertEquals(true, $matcher->matches($this->getContext(0)));
        $this->assertEquals(true, $matcher->matches($this->getContext(1)));
        $this->assertEquals(true, $matcher->matches($this->getContext(2)));
        $this->assertEquals(true, $matcher->matches($this->getContext(100)));
    }

    /**
     * @test
     */
    public function test()
    {
        $matcher = new DepthMatcher(2);
        $this->assertEquals(true,  $matcher->matches($this->getContext(1)));
        $this->assertEquals(true, $matcher->matches($this->getContext(2)));
        $this->assertEquals(false, $matcher->matches($this->getContext(3)));
    }

    /**
     * @param int $depth
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getContext($depth)
    {
        $context = $this->getMockBuilder('JMS\Serializer\Context')->disableOriginalConstructor()->getMockForAbstractClass();
        $context
            ->expects($this->any())
            ->method('getDepth')
            ->will($this->returnValue($depth));

        return $context;
    }
}
