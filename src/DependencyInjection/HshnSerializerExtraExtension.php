<?php

namespace Hshn\SerializerExtraBundle\DependencyInjection;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class HshnSerializerExtraExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if ($config['roles']) {
            $this->loadRoles($container, $loader, $config['roles']);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param LoaderInterface  $loader
     * @param array            $config
     */
    private function loadRoles(ContainerBuilder $container, LoaderInterface $loader, array $config)
    {
        $loader->load('roles.xml');

        $roleManager = $container->getDefinition('hshn.serializer_extra.attribute_manager');
        foreach ($config['classes'] as $class) {
            $roleManager->addMethodCall('addAttributes', [$class['class'], $class['attributes']]);
        }

        $roleSubscriber = new DefinitionDecorator('hshn.serializer_extra.role_subscriber.def');
        $roleSubscriber->addArgument($config['export_to']);
        $roleSubscriber->addTag('jms_serializer.event_subscriber');

        $container->setDefinition('hshn.serializer_extra.role_subscriber', $roleSubscriber);
    }
}
