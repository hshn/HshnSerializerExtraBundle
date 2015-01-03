<?php

namespace Hshn\SerializerExtraBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

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

        $isFilterSpecified = false;
        $configurations = [];
        foreach ($config['classes'] as $class => $vars) {
            $id = sprintf('hshn.serializer_extra.vich_uploader.configuration.%s', md5($class));

            $definition = new DefinitionDecorator('hshn.serializer_extra.vich_uploader.configuration');
            $definition->setArguments([$class, $this->createVichUploaderFileConfig($container, $id, $vars['files'], $isFilterSpecified), $vars['max_depth']]);
            $container->setDefinition($id, $definition);

            $configurations[] = new Reference($id);
        }

        $container->getDefinition('hshn.serializer_extra.vich_uploader.configuration_repository')->addArgument($configurations);

        if ($isFilterSpecified) {
            $this->loadLiipImagineFilter($container, $loader);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $prefix
     * @param array            $files
     * @param bool             $isFilterSpecified
     *
     * @return \Symfony\Component\DependencyInjection\Reference[]
     */
    private function createVichUploaderFileConfig(ContainerBuilder $container, $prefix, array $files, &$isFilterSpecified)
    {
        $references = [];
        foreach ($files as $i => $file) {
            $id = "{$prefix}.file{$i}";
            $container->setDefinition($id, $definition = new DefinitionDecorator('hshn.serializer_extra.vich_uploader.configuration.file'));
            $definition->setArguments([$file['property'], $file['export_to'], $file['filter']]);

            $references[] = new Reference($id);
            $isFilterSpecified = $isFilterSpecified || $file['filter'] !== null;
        }

        return $references;
    }

    /**
     * @param ContainerBuilder $container
     * @param LoaderInterface  $loader
     */
    private function loadLiipImagineFilter(ContainerBuilder $container, LoaderInterface $loader)
    {
        $this->ensureBundleEnabled($container, 'LiipImagineBundle');
        $loader->load('liip_imagine.xml');

        $container->setAlias('hshn.serializer_extra.vich_uploader.uri_resolver', 'hshn.serializer_extra.vich_uploader.uri_resolver.imagine_filter');
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
