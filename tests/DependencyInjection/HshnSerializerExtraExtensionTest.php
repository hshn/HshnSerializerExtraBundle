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
     * @expectedException \RuntimeException
     * @expectedExceptionMessage LiipImagineBundle
     */
    public function testThrowsExceptionWhenUsingFiltersUnlessLiipImagineBundleIsEnabled()
    {
        $this->enableBundle(['VichUploaderBundle']);
        $this->loadExtension([
            'vich_uploader' => [
                'classes' => [
                    'Foo' => [
                        'files' => [
                            ['property' => 'bar', 'filter' => 'baz'],
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * @test
     */
    public function testVichUploaderConfigsWithoutFilter()
    {
        $this->enableBundle(['VichUploaderBundle']);
        $this->loadExtension([
            'vich_uploader' => [
                'classes' => [
                    'Foo' => [
                        'files' => [
                            ['property' => 'bar'],
                        ],
                    ],
                ],
            ],
        ]);

        $alias = $this->container->getAlias('hshn.serializer_extra.vich_uploader.uri_resolver');
        $this->assertEquals('hshn.serializer_extra.vich_uploader.uri_resolver.storage', (string) $alias);
    }

    /**
     * @test
     */
    public function testVichUploaderConfigs()
    {
        $this->enableBundle(['VichUploaderBundle', 'LiipImagineBundle']);
        $this->loadExtension([
            'vich_uploader' => [
                'classes' => [
                    'Foo' => [
                        'files' => [
                            ['property' => 'foo', 'export_to' => 'bar'],
                            ['property' => 'foo', 'filter' => 'baz']
                        ],
                        'max_depth' => 1,
                    ],
                    'Bar' => [
                        'files' => [
                            ['property' => 'foo']
                        ],
                    ],
                ],
            ]
        ]);

        $alias = $this->container->getAlias('hshn.serializer_extra.vich_uploader.uri_resolver');
        $this->assertEquals('hshn.serializer_extra.vich_uploader.uri_resolver.imagine_filter', (string) $alias);

        $this->container->hasDefinition('hshn.serializer_extra.vich_uploader.configuration_repository');
        $definition = $this->container->getDefinition('hshn.serializer_extra.vich_uploader.configuration_repository');
        $this->assertCount(2, $configurations = $definition->getArgument(0));

        $this->assertVichUploaderConfig($configurations[0], 'Foo', [
            $this->isFileConfiguration('foo', 'bar'),
            $this->isFileConfiguration('foo', null, 'baz')
        ], 1);

        $this->assertVichUploaderConfig($configurations[1], 'Bar', [
            $this->isFileConfiguration('foo')
        ]);
    }

    /**
     * @param Reference                       $reference
     * @param string                          $expectedClass
     * @param \PHPUnit_Framework_Constraint[] $expectedFiles
     * @param int                             $expectedMaxDepth
     * @param string                          $message
     */
    private function assertVichUploaderConfig(Reference $reference, $expectedClass, array $expectedFiles = [], $expectedMaxDepth = -1, $message = '')
    {
        $id = (string) $reference;
        $this->assertRegExp('/^hshn\.serializer_extra\.vich_uploader\.configuration\.\w+$/', $id, $message);

        $definition = $this->container->getDefinition($id);
        $this->assertEquals($expectedClass, $definition->getArgument(0), $message);
        $this->assertCount(count($expectedFiles), $files = $definition->getArgument(1));
        foreach ($files as $i => $file) {
            $this->assertThat($file, $expectedFiles[$i], $message);
        }

        $this->assertEquals($expectedMaxDepth, $definition->getArgument(2));
    }

    /**
     * @param string $property
     * @param string $exportTo
     * @param string $filter
     *
     * @return \PHPUnit_Framework_Constraint
     */
    private function isFileConfiguration($property, $exportTo = null, $filter = null)
    {
        return $this->logicalAnd(
            $this->isInstanceOf('Symfony\Component\DependencyInjection\Reference'),
            $this->callback(function (Reference $reference) use ($property, $exportTo, $filter) {
                $id = (string) $reference;
                $this->assertRegExp('/^hshn\.serializer_extra\.vich_uploader\.configuration\.\w+\.file\d+$/', $id);

                $definition = $this->container->getDefinition($id);
                $this->assertEquals($property, $definition->getArgument(0));
                $this->assertEquals($exportTo, $definition->getArgument(1));
                $this->assertEquals($filter, $definition->getArgument(2));

                return true;
            })
        );
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
