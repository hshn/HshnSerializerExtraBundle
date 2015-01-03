<?php

namespace Hshn\SerializerExtraBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;

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
        $this->loadExtension([]);

        $this->assertFalse($this->container->hasDefinition('hshn.serializer_extra.authority.event_subscriber'));
    }

    /**
     * @test
     */
    public function testAuthorityConfigs()
    {
        $this->loadExtension([
            'authority' => [
                'classes' => [
                    'Foo' => ['attributes' => 'ROLE_A'],
                    'Bar' => ['attributes' => ['ROLE_A', 'ROLE_B']],
                ]
            ]
        ]);

        $this->assertTrue($this->container->hasDefinition('hshn.serializer_extra.authority.event_subscriber'));
        $definition = $this->container->getDefinition('hshn.serializer_extra.authority.event_subscriber');
        $this->assertEquals('_authority', $definition->getArgument(0));

        $definition = $this->container->getDefinition('hshn.serializer_extra.authority.configuration_repository');

        $methodCalls = $definition->getMethodCalls();
        $this->assertMethodCall($methodCalls[0], 'set', ['Foo', $this->isInstanceOf('Symfony\Component\DependencyInjection\Reference')]);
        $this->assertMethodCall($methodCalls[1], 'set', ['Bar', $this->isInstanceOf('Symfony\Component\DependencyInjection\Reference')]);
    }

    /**
     * @test
     */
    public function testOverridingAuthorityConfigs()
    {
        $this->loadExtension([
            'authority' => [
                'export_to' => 'my_authority',
            ]
        ]);

        $this->assertTrue($this->container->hasDefinition('hshn.serializer_extra.authority.event_subscriber'));
        $definition = $this->container->getDefinition('hshn.serializer_extra.authority.event_subscriber');
        $this->assertEquals('my_authority', $definition->getArgument(0));
    }

    /**
     * @test
     * @expectedException \RuntimeException
     * @expectedExceptionMessage VichUploaderBundle
     */
    public function testThrowExceptionUnlessVichUploaderBundleIsEnabled()
    {
        $this->loadExtension([
            'vich_uploader' => []
        ]);
    }

    /**
     * @test
     */
    public function testVichUploaderConfigs()
    {
        $this->enableBundle(['VichUploaderBundle']);
        $this->loadExtension([
            'vich_uploader' => [
                'classes' => [
                    'MyClass_A' => [
                        'attributes' => [
                            'foo' => 'bar',
                            'name' => null,
                        ],
                        'max_depth' => 1,
                    ],
                    'MyClass_B' => null,
                ],
            ]
        ]);

        $this->container->hasDefinition('hshn.serializer_extra.vich_uploader.configuration_repository');
        $definition = $this->container->getDefinition('hshn.serializer_extra.vich_uploader.configuration_repository');
        $this->assertCount(2, $definition->getArgument(0));
    }

    /**
     * @param array $bundles
     */
    private function enableBundle(array $bundles)
    {
        $enabledBundles = [];
        try {
            $enabledBundles = $this->container->getParameter('kernel.bundles');
        } catch (InvalidArgumentException $e) {
        }

        foreach ($bundles as $bundle) {
            $enabledBundles[$bundle] = true;
        }

        $this->container->setParameter('kernel.bundles', $enabledBundles);
    }

    /**
     * @param array $config
     */
    private function loadExtension(array $config)
    {
        $this->enableBundle([]);
        $this->extension->load([
            'hshn_serializer_extra' => $config
        ], $this->container);
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
