<?php

namespace Hshn\SerializerExtraBundle\Authority\AuthorizationChecker;

/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class AuthorizationCheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testIsGranted()
    {
        if (!class_exists('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface')) {
            $this->markTestSkipped('Only Symfony#2.6<=');

            return;
        }

        $checker = new AuthorizationChecker(
            $delegate = $this->getMock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface')
        );

        $delegate
            ->expects($this->once())
            ->method('isGranted')
            ->with($attribute = 'attribute', $object = new \stdClass())
            ->will($this->returnValue($returnValue = 'return value'));

        $this->assertEquals($returnValue, $checker->isGranted($attribute, $object));
    }
}
