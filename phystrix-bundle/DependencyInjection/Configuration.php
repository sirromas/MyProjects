<?php

namespace Odesk\Bundle\PhystrixBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration provides schema for the bundle configuration
 * @package Odesk\Bundle\PhystrixBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('phystrix');

        $rootNode
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->children()
                    ->arrayNode('fallback')->canBeEnabled()->end()
                    ->arrayNode('circuitBreaker')
                        ->addDefaultsIfNotSet()
                        ->canBeEnabled()
                        ->children()
                            ->integerNode('errorThresholdPercentage')->defaultValue(50)->end()
                            ->integerNode('requestVolumeThreshold')->defaultValue(20)->end()
                            ->integerNode('sleepWindowInMilliseconds')->defaultValue(5000)->end()
                            ->booleanNode('forceOpen')->defaultValue(false)->end()
                            ->booleanNode('forceClosed')->defaultValue(false)->end()
                        ->end()
                    ->end()
                    ->arrayNode('metrics')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->integerNode('healthSnapshotIntervalInMilliseconds')->defaultValue(1000)->end()
                            ->integerNode('rollingStatisticalWindowInMilliseconds')->defaultValue(1000)->end()
                            ->integerNode('rollingStatisticalWindowBuckets')->defaultValue(10)->end()
                        ->end()
                    ->end()
                    ->arrayNode('requestCache')->canBeDisabled()->end()
                    ->arrayNode('requestLog')->canBeEnabled()->end()
                ->end()
            ->end()
            ->validate()
                ->ifTrue(function ($data) {
                        return !array_key_exists('default', $data);
                })
                ->thenInvalid("'default' configuration should be set")
            ->end();

        return $treeBuilder;
    }
}
