<?php

namespace Streply\StreplyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('streply');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('dsn')->defaultValue('%env(STREPLY_DSN)%')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
