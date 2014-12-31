<?php


namespace Hshn\SerializerExtraBundle\VichUploader;



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
    private $matcherFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mappingFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $storage;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $configurationRepository;

    protected function setUp()
    {
        $this->subscriber = new EventSubscriber(
            $this->matcherFactory = $this->getMock('Hshn\SerializerExtraBundle\ContextMatcher\MatcherFactory'),
            $this->mappingFactory = $this->getMockBuilder('Vich\UploaderBundle\Mapping\PropertyMappingFactory')->disableOriginalConstructor()->getMock(),
            $this->storage = $this->getMock('Vich\UploaderBundle\Storage\StorageInterface'),
            $this->configurationRepository = $this->getMockBuilder('Hshn\SerializerExtraBundle\VichUploader\ConfigurationRepository')->disableOriginalConstructor()->getMock()
        );
    }

    /**
     * @test
     */
    public function testDoNotExportUnlessSupportedType()
    {
        $event = $this->getObjectEvent('mocked type');
        $event
            ->expects($this->never())
            ->method('getVisitor');

        $this
            ->configurationRepository
            ->expects($this->once())
            ->method('get')
            ->with('mocked type')
            ->will($this->throwException(new \InvalidArgumentException()));

        $this->subscriber->onPostSerialize($event);
    }

    /**
     * @test
     */
    public function testDoNotExportUnlessMatchesContextMatcher()
    {
        $event = $this->getObjectEvent('mocked type');
        $event
            ->expects($this->never())
            ->method('getVisitor');

        $this->setConfigurationAs($configuration = $this->getConfiguration(2), 'mocked type');

        $this
            ->matcherFactory
            ->expects($this->once())
            ->method('depth')
            ->with(2)
            ->will($this->returnValue($this->getMatcher(false)));

        $event
            ->expects($this->once())
            ->method('getContext')
            ->will($this->returnValue($this->getContext()));

        $this->subscriber->onPostSerialize($event);
    }

    /**
     * @test
     */
    public function testExportUsingAttributes()
    {
        $event = $this->getObjectEvent('mocked type');

        $this->setConfigurationAs($configuration = $this->getConfiguration(2), 'mocked type');

        $this
            ->matcherFactory
            ->expects($this->once())
            ->method('depth')
            ->with(2)
            ->will($this->returnValue($this->getMatcher(true)));

        $event
            ->expects($this->once())
            ->method('getContext')
            ->will($this->returnValue($context = $this->getContext()));

        $configuration
            ->expects($this->atLeastOnce())
            ->method('getClass')
            ->will($this->returnValue($class = 'mocked class'));

        $event
            ->expects($this->once())
            ->method('getVisitor')
            ->will($this->returnValue($visitor = $this->getJsonSerializationVisitor()));

        $event
            ->expects($this->once())
            ->method('getObject')
            ->will($this->returnValue($object = new \stdClass()));

        $configuration
            ->expects($this->once())
            ->method('getAttributes')
            ->will($this->returnValue([
                'foo' => 'bar',
                'bar' => 'baz',
            ]));

        $this
            ->storage
            ->expects($this->exactly(2))
            ->method('resolveUri')
            ->with($this->identicalTo($object), $this->logicalOr('foo', 'bar'), $class)
            ->will($this->onConsecutiveCalls('http://foo', 'http://bar'));

        $visitor
            ->expects($this->exactly(2))
            ->method('addData')
            ->with($this->logicalOr('bar', 'baz'), $this->logicalOr('http://foo', 'http://bar'));

        $this->subscriber->onPostSerialize($event);
    }

    /**
     * @test
     */
    public function testExportUsingNoAttributes()
    {
        $event = $this->getObjectEvent('mocked type');

        $this->setConfigurationAs($configuration = $this->getConfiguration(2), 'mocked type');

        $this
            ->matcherFactory
            ->expects($this->once())
            ->method('depth')
            ->with(2)
            ->will($this->returnValue($this->getMatcher(true)));

        $event
            ->expects($this->once())
            ->method('getContext')
            ->will($this->returnValue($context = $this->getContext()));

        $configuration
            ->expects($this->atLeastOnce())
            ->method('getClass')
            ->will($this->returnValue($class = 'mocked class'));

        $event
            ->expects($this->once())
            ->method('getVisitor')
            ->will($this->returnValue($visitor = $this->getJsonSerializationVisitor()));

        $event
            ->expects($this->once())
            ->method('getObject')
            ->will($this->returnValue($object = new \stdClass()));

        $configuration
            ->expects($this->once())
            ->method('getAttributes')
            ->will($this->returnValue([]));

        $this
            ->mappingFactory
            ->expects($this->once())
            ->method('fromObject')
            ->with($this->identicalTo($object), $class)
            ->will($this->returnValue([
                $this->getPropertyMapping('foo'),
                $this->getPropertyMapping('bar'),
            ]));

        $this
            ->storage
            ->expects($this->exactly(2))
            ->method('resolveUri')
            ->with($this->identicalTo($object), $this->logicalOr('foo', 'bar'), $class)
            ->will($this->onConsecutiveCalls('http://foo', 'http://bar'));

        $visitor
            ->expects($this->exactly(2))
            ->method('addData')
            ->with($this->logicalOr('foo', 'bar'), $this->logicalOr('http://foo', 'http://bar'));

        $this->subscriber->onPostSerialize($event);
    }

    /**
     * @param Configuration $configuration
     * @param string        $type
     */
    private function setConfigurationAs(Configuration $configuration, $type)
    {
        $this
            ->configurationRepository
            ->expects($this->atLeastOnce())
            ->method('get')
            ->with($type)
            ->will($this->returnValue($configuration));
    }

    /**
     * @param string $filePropertyName
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getPropertyMapping($filePropertyName)
    {
        $mapping = $this->getMockBuilder('Vich\UploaderBundle\Mapping\PropertyMapping')->disableOriginalConstructor()->getMock();
        $mapping
            ->expects($this->atLeastOnce())
            ->method('getFilePropertyName')
            ->will($this->returnValue($filePropertyName));

        return $mapping;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getJsonSerializationVisitor()
    {
        return $this->getMockBuilder('JMS\Serializer\JsonSerializationVisitor')->disableOriginalConstructor()->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getContext()
    {
        return $this->getMockBuilder('JMS\Serializer\Context')->disableOriginalConstructor()->getMockForAbstractClass();
    }

    /**
     * @param bool $matches
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getMatcher($matches = true)
    {
        $matcher = $this->getMock('Hshn\SerializerExtraBundle\ContextMatcher\MatcherInterface');

        $matcher
            ->expects($this->atLeastOnce())
            ->method('matches')
            ->with($this->isInstanceOf('JMS\Serializer\Context'))
            ->will($this->returnValue($matches));

        return $matcher;
    }

    /**
     * @param $maxDepth
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getConfiguration($maxDepth)
    {
        $configuration = $this->getMockBuilder('Hshn\SerializerExtraBundle\VichUploader\Configuration')->disableOriginalConstructor()->getMock();
        $configuration
            ->expects($this->atLeastOnce())
            ->method('getMaxDepth')
            ->will($this->returnValue($maxDepth));

        return $configuration;
    }

    /**
     * @param string $type
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getObjectEvent($type)
    {
        $event = $this->getMockBuilder('JMS\Serializer\EventDispatcher\ObjectEvent')->disableOriginalConstructor()->getMock();
        $event
            ->expects($this->any())
            ->method('getType')
            ->will($this->returnValue(['name' => $type]));

        return $event;
    }
}
