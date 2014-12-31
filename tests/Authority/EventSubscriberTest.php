<?php


namespace Hshn\SerializerExtraBundle\Authority;

use Hshn\SerializerExtraBundle\ContextMatcher\MatcherFactory;


/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class EventSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EventSubscriber
     */
    private $subscriber;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $authorizationChecker;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $configurationRepository;

    protected function setUp()
    {
        $this->subscriber = new EventSubscriber(
            new MatcherFactory(),
            $this->authorizationChecker = $this->getMock('Hshn\SerializerExtraBundle\Authority\AuthorizationCheckerInterface'),
            $this->configurationRepository = $this->getMockBuilder('Hshn\SerializerExtraBundle\Authority\ConfigurationRepository')->getMock()
        );
    }

    /**
     * @test
     */
    public function testAddRoleStates()
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
            ->configurationRepository
            ->expects($this->once())
            ->method('get')
            ->with($type)
            ->will($this->returnValue($config = $this->getMock('Hshn\SerializerExtraBundle\Authority\Configuration')));

        $config
            ->expects($this->once())
            ->method('getAttributes')
            ->will($this->returnValue(array_keys($roles)));

        $config
            ->expects($this->once())
            ->method('getMaxDepth')
            ->will($this->returnValue(-1));

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
     * @test
     */
    public function testAddNoRoleStatesUnlessAddAttributes()
    {
        $event = $this->getMockBuilder('JMS\Serializer\EventDispatcher\ObjectEvent')->disableOriginalConstructor()->getMock();
        $event
            ->expects($this->once())
            ->method('getType')
            ->will($this->returnValue(['name' => $type = 'Foo']));

        $this
            ->configurationRepository
            ->expects($this->once())
            ->method('get')
            ->with($type)
            ->will($this->throwException(new \InvalidArgumentException()));

        $this
            ->authorizationChecker
            ->expects($this->never())
            ->method('isGranted');

        $event
            ->expects($this->never())
            ->method('getContext');

        $this->subscriber->onPostSerialize($event);
    }

    /**
     * @test
     */
    public function testAddNoRoleStatesWhenReachedMaxDepth()
    {
        $event = $this->getMockBuilder('JMS\Serializer\EventDispatcher\ObjectEvent')->disableOriginalConstructor()->getMock();
        $event
            ->expects($this->once())
            ->method('getType')
            ->will($this->returnValue(['name' => $type = 'Foo']));

        $this
            ->configurationRepository
            ->expects($this->once())
            ->method('get')
            ->with($type)
            ->will($this->returnValue($config = $this->getMock('Hshn\SerializerExtraBundle\Authority\Configuration')));

        $config
            ->expects($this->once())
            ->method('getMaxDepth')
            ->will($this->returnValue(1));


        $event
            ->expects($this->once())
            ->method('getContext')
            ->will($this->returnValue($context = $this->getMock('JMS\Serializer\Context')));

        $context
            ->expects($this->once())
            ->method('getDepth')
            ->will($this->returnValue(2));

        $this
            ->authorizationChecker
            ->expects($this->never())
            ->method('isGranted');;

        $this->subscriber->onPostSerialize($event);
    }
}
