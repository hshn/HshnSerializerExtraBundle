<?php

namespace Hshn\SerializerExtraBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class HshnSerializerExtraExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HshnSerializerExtraExtension
     */
    private $extension;

    /**
     * @var ContainerBuilder
     */
    private $container;

    protected function setUp()
    {
        $this->extension = new HshnSerializerExtraExtension();
        $this->container = new ContainerBuilder();
    }

    /**
     * @test
     */
    public function testWithoutConfigs()
    {
        $this->extension->load([], $this->container);

        $this->assertFalse($this->container->hasDefinition('hshn.serializer_extra.roles.event_subscriber'));
    }

    /**
     * @test
     */
    public function testRoleConfigs()
    {
        $this->extension->load([
            'hshn_serializer_extra' => [
                'roles' => [
                    'classes' => [
                        'Foo' => ['attributes' => 'ROLE_A'],
                        'Bar' => ['attributes' => ['ROLE_A', 'ROLE_B']],
                    ]
                ]
            ]
        ], $this->container);

        $this->assertTrue($this->container->hasDefinition('hshn.serializer_extra.roles.event_subscriber'));
        $definition = $this->container->getDefinition('hshn.serializer_extra.roles.event_subscriber');
        $this->assertEquals('_roles', $definition->getArgument(0));

        $definition = $this->container->getDefinition('hshn.serializer_extra.roles.configuration_repository');

        $methodCalls = $definition->getMethodCalls();
        $this->assertMethodCall($methodCalls[0], 'set', ['Foo', $this->isInstanceOf('Symfony\Component\DependencyInjection\Reference')]);
        $this->assertMethodCall($methodCalls[1], 'set', ['Bar', $this->isInstanceOf('Symfony\Component\DependencyInjection\Reference')]);
    }

    /**
     * @test
     */
    public function testOverridingRoleConfigs()
    {
        $this->extension->load([
            'hshn_serializer_extra' => [
                'roles' => [
                    'export_to' => 'my_roles',
                ]
            ]
        ], $this->container);

        $this->assertTrue($this->container->hasDefinition('hshn.serializer_extra.roles.event_subscriber'));
        $definition = $this->container->getDefinition('hshn.serializer_extra.roles.event_subscriber');
        $this->assertEquals('my_roles', $definition->getArgument(0));
    }

    /**
     * @param array  $methodCall
     * @param string $name
     * @param array  $expectedValues
     */
    private function assertMethodCall(array $methodCall, $name, array $expectedValues)
    {
        $this->assertEquals($name, $methodCall[0], "Failed asserting that method {$name} was called");

        foreach ($methodCall[1] as $key => $parameter) {
            $expectedValue = $expectedValues[$key];

            if (! $expectedValue instanceof \PHPUnit_Framework_Constraint) {
                $expectedValue = $this->equalTo($expectedValue);
            }

            $this->assertThat($parameter, $expectedValue);
        }
    }
}
