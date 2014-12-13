<?php

namespace Hshn\SerializerExtraBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('hshn_serializer_extra');

        $rootNode
            ->children()
                ->arrayNode('roles')
                    ->children()
                        ->scalarNode('export_to')->defaultValue('_roles')->end()
                        ->arrayNode('classes')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('class')->isRequired()->end()
                                    ->arrayNode('attributes')
                                        ->isRequired()
                                        ->prototype('scalar')->end()
                                        ->beforeNormalization()
                                            ->ifString()
                                            ->then(function ($v) {
                                                return [$v];
                                            })
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
