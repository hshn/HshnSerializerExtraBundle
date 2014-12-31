<?php


namespace Hshn\SerializerExtraBundle\Authority\AuthorizationChecker;


/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class SecurityContextCheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SecurityContextChecker
     */
    private $checker;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $securityContext;

    protected function setUp()
    {
    }

    /**
     * @test
     */
    public function testIsGranted()
    {
        if (class_exists('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface')) {
            $this->markTestSkipped('Only Symfony#2.6>');

            return;
        }

        $checker = new SecurityContextChecker(
            $securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface')
        );

        $securityContext
            ->expects($this->once())
            ->method('isGranted')
            ->with($attribute = 'attribute', $object = new \stdClass())
            ->will($this->returnValue($returnValue = 'return value'));

        $this->assertEquals($returnValue, $checker->isGranted($attribute, $object));
    }
}
