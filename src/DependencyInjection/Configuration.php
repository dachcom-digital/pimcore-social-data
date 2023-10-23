<?php

namespace SocialDataBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('social_data');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('social_post_data_class')->defaultValue(null)->cannotBeEmpty()->end()
                ->arrayNode('maintenance')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('clean_up_logs')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultValue(true)->end()
                                ->integerNode('expiration_days')->defaultValue(30)->end()
                            ->end()
                        ->end()
                        ->arrayNode('clean_up_old_posts')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultValue(false)->end()
                                ->booleanNode('delete_poster')->defaultValue(false)->end()
                                ->integerNode('expiration_days')->defaultValue(150)->end()
                            ->end()
                        ->end()
                        ->arrayNode('fetch_social_post')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultValue(false)->end()
                                ->floatNode('interval_in_hours')->defaultValue(6)->min(0.1)->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('available_connectors')
                    ->prototype('array')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('connector_name')->cannotBeEmpty()->isRequired()->end()
                            ->variableNode('connector_config')->defaultValue([])->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        $rootNode->append($this->createPersistenceNode());

        return $treeBuilder;
    }

    private function createPersistenceNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('persistence');
        $node = $treeBuilder->getRootNode();

        $node
            ->addDefaultsIfNotSet()
            ->performNoDeepMerging()
            ->children()
                ->arrayNode('doctrine')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('entity_manager')
                            ->info('Name of the entity manager that you wish to use for managing form builder entities.')
                            ->cannotBeEmpty()
                            ->defaultValue('default')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
