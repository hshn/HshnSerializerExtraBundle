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
        $loader->load('service.xml');

        if (isset($config['authority'])) {
            $this->loadAuthority($container, $loader, $config['authority']);
        }

        if (isset($config['vich_uploader'])) {
            $this->loadVichUploader($container, $loader, $config['vich_uploader']);
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

        $authorizationChecker = class_exists('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface') ? 'default' : 'security_context';

        $container->setAlias('hshn.serializer_extra.authority.authorization_checker', sprintf('hshn.serializer_extra.authority.authorization_checker.%s', $authorizationChecker));
    }

    /**
     * @param ContainerBuilder $container
     * @param LoaderInterface  $loader
     * @param array            $config
     */
    private function loadVichUploader(ContainerBuilder $container, LoaderInterface $loader, array $config)
    {
        $this->ensureBundleEnabled($container, 'VichUploaderBundle');

        $loader->load('vich_uploader.xml');

        $configurations = [];
        foreach ($config['classes'] as $class => $vars) {
            $id = sprintf('hshn.serializer_extra.vich_uploader.configuration.%s', md5($class));
            $container->setDefinition($id, $definition = new DefinitionDecorator('hshn.serializer_extra.vich_uploader.configuration'));
            $definition
                ->addArgument($class)
                ->addArgument($vars['attributes'])
                ->addArgument($vars['max_depth']);

            $configurations[] = new Reference($id);
        }

        $container->getDefinition('hshn.serializer_extra.vich_uploader.configuration_repository')->addArgument($configurations);
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $bundle
     */
    private function ensureBundleEnabled(ContainerBuilder $container, $bundle)
    {
        $bundles = $container->getParameter('kernel.bundles');
        if (!isset($bundles[$bundle])) {
            throw new \RuntimeException(sprintf('The HshnSerializerExtraBundle requires the %s to enable integration of it, please make sure to enable it in your AppKernel', $bundle));
        }
    }
}
