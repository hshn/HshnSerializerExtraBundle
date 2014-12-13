<?php


namespace Hshn\SerializerExtraBundle\Role;



/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class RoleSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RoleSubscriber
     */
    private $subscriber;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $authorizationChecker;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $roleManager;

    protected function setUp()
    {
        $this->subscriber = new RoleSubscriber(
            $this->authorizationChecker = $this->getMock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface'),
            $this->roleManager = $this->getMockBuilder('Hshn\SerializerExtraBundle\Role\AttributeManager')->getMock()
        );
    }

    /**
     * @test
     */
    public function testOnPostSerialize()
    {
        $event = $this->getMockBuilder('JMS\Serializer\EventDispatcher\ObjectEvent')->disableOriginalConstructor()->getMock();
        $event
            ->expects($this->once())
            ->method('getType')
            ->will($this->returnValue(['name' => $type = 'Foo']));

        $roles = [
            'foo' => true,
            'bar' => false,
        ];

        $this
            ->roleManager
            ->expects($this->once())
            ->method('getAttributes')
            ->with($type)
            ->will($this->returnValue(array_keys($roles)));

        $event
            ->expects($this->any())
            ->method('getObject')
            ->will($this->returnValue($object = new \stdClass()));

        $this
            ->authorizationChecker
            ->expects($this->exactly(count($roles)))
            ->method('isGranted')
            ->with(call_user_func_array([$this, 'logicalOr'], array_keys($roles)), $this->identicalTo($object))
            ->will($this->returnCallback(function ($attribute) use ($roles) {
                return $roles[$attribute];
            }));

        $event
            ->expects($this->once())
            ->method('getContext')
            ->will($this->returnValue($context = $this->getMockBuilder('JMS\Serializer\Context')->disableOriginalConstructor()->getMock()));

        $context
            ->expects($this->once())
            ->method('getVisitor')
            ->will($this->returnValue($visitor = $this->getMockBuilder('JMS\Serializer\JsonSerializationVisitor')->disableOriginalConstructor()->getMock()));

        $visitor
            ->expects($this->once())
            ->method('addData')
            ->with('_roles', $roles);

        $this->subscriber->onPostSerialize($event);
    }

    /**
     * @return array
     */
    public function provideOnPostSerializeTests()
    {
        // [attributes, ]
        return [
            []
        ];
    }
}
