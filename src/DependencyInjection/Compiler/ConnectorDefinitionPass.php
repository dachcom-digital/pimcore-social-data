<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

namespace SocialDataBundle\DependencyInjection\Compiler;

use SocialDataBundle\Registry\ConnectorDefinitionRegistry;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ConnectorDefinitionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $definition = $container->getDefinition(ConnectorDefinitionRegistry::class);
        foreach ($container->findTaggedServiceIds('social_data.connector_definition', true) as $id => $tags) {
            $connectorDefinition = $container->getDefinition($id);
            foreach ($tags as $attributes) {
                if (!isset($attributes['identifier'])) {
                    throw new InvalidConfigurationException(sprintf('You need to define a valid identifier for connector "%s"', $id));
                } elseif (!isset($attributes['socialPostBuilder'])) {
                    throw new InvalidConfigurationException(sprintf('You need to define a valid social post builder service for connector "%s"', $id));
                } elseif ($container->hasDefinition($attributes['socialPostBuilder']) === false) {
                    throw new InvalidConfigurationException(sprintf('Social post builder service "%s" for connector "%s" not found', $attributes['collector'], $id));
                }

                $connectorConfigurationName = sprintf('social_data.connectors.system_config.%s', $attributes['identifier']);
                $connectorDefinitionConfiguration = $container->hasParameter($connectorConfigurationName) ? $container->getParameter($connectorConfigurationName) : [];

                $connectorDefinition->addMethodCall('setDefinitionConfiguration', [$connectorDefinitionConfiguration]);
                $connectorDefinition->addMethodCall('setSocialPostBuilder', [$container->getDefinition($attributes['socialPostBuilder'])]);

                $definition->addMethodCall('register', [new Reference($id), $attributes['identifier']]);
            }
        }
    }
}
