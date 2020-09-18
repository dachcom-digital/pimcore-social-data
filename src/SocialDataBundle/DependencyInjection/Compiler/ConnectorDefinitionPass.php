<?php

namespace SocialDataBundle\DependencyInjection\Compiler;

use SocialDataBundle\Registry\ConnectorDefinitionRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ConnectorDefinitionPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition(ConnectorDefinitionRegistry::class);
        foreach ($container->findTaggedServiceIds('social_data.connector_definition', true) as $id => $tags) {
            $connectorDefinition = $container->getDefinition($id);
            foreach ($tags as $attributes) {
                $connectorDefinitionConfiguration = $container->getParameter(sprintf('social_data.connectors.system_config.%s', $attributes['identifier']));
                $connectorDefinition->addMethodCall('setDefinitionConfiguration', [$connectorDefinitionConfiguration]);
                $definition->addMethodCall('register', [new Reference($id), $attributes['identifier']]);
            }
        }
    }
}
