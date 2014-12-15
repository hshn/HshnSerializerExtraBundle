<?php

namespace Hshn\SerializerExtraBundle\DependencyInjection;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
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

        if (isset($config['authority'])) {
            $this->loadAuthority($container, $loader, $config['authority']);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param LoaderInterface  $loader
     * @param array            $config
     */
    private function loadAuthority(ContainerBuilder $container, LoaderInterface $loader, array $config)
    {
        $loader->load('authority.xml');

        $repository = $container->getDefinition('hshn.serializer_extra.authority.configuration_repository');
        foreach ($config['classes'] as $class => $vars) {
            $id = sprintf('hshn.serializer_extra.authority.configuration.%s', md5($class));
            $container->setDefinition($id, $definition = new DefinitionDecorator('hshn.serializer_extra.authority.configuration'));

            $definition
                ->addArgument($vars['attributes'])
                ->addArgument($vars['max_depth']);

            $repository->addMethodCall('set', [$class, new Reference($id)]);
        }

        $roleSubscriber = new DefinitionDecorator('hshn.serializer_extra.authority.event_subscriber.def');
        $roleSubscriber->addArgument($config['export_to']);
        $roleSubscriber->addTag('jms_serializer.event_subscriber');

        $container->setDefinition('hshn.serializer_extra.authority.event_subscriber', $roleSubscriber);
    }
}
