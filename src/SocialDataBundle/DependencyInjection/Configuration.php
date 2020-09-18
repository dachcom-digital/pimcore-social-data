<?php

namespace SocialDataBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('social_data');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('social_post_data_class')->defaultValue(null)->cannotBeEmpty()->end()
                ->integerNode('log_expiration_days')->defaultValue(30)->end()
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

    private function createPersistenceNode()
    {
        $treeBuilder = new TreeBuilder('persistence');
        $node = $treeBuilder->root('persistence');

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
