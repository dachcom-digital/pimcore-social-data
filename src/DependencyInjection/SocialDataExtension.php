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

namespace SocialDataBundle\DependencyInjection;

use SocialDataBundle\Service\EnvironmentService;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SocialDataExtension extends Extension
{
    /**
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator([__DIR__ . '/../../config']));
        $loader->load('services.yaml');

        $persistenceConfig = $config['persistence']['doctrine'];
        $entityManagerName = $persistenceConfig['entity_manager'];

        $container->setParameter('social_data.persistence.doctrine.enabled', true);
        $container->setParameter('social_data.persistence.doctrine.manager', $entityManagerName);

        $availableConnectorsNames = [];
        foreach ($config['available_connectors'] as $availableConnector) {
            $availableConnectorsNames[] = $availableConnector['connector_name'];
            $container->setParameter(sprintf('social_data.connectors.system_config.%s', $availableConnector['connector_name']), $availableConnector['connector_config']);
        }

        $container->setParameter('social_data.connectors.available', $availableConnectorsNames);

        $this->setupMaintenanceSetting($container, $config);
        $this->setupEnvironment($container, $config);
    }

    protected function setupMaintenanceSetting(ContainerBuilder $container, array $config): void
    {
        $maintenanceSettings = $config['maintenance'];

        $container->setParameter('social_data.maintenance.clean_up_old_posts.enabled', $maintenanceSettings['clean_up_old_posts']['enabled']);
        $container->setParameter('social_data.maintenance.clean_up_old_posts.delete_poster', $maintenanceSettings['clean_up_old_posts']['delete_poster']);
        $container->setParameter('social_data.maintenance.clean_up_old_posts.expiration_days', $maintenanceSettings['clean_up_old_posts']['expiration_days']);

        $container->setParameter('social_data.maintenance.clean_up_logs.enabled', $maintenanceSettings['clean_up_logs']['enabled']);
        $container->setParameter('social_data.maintenance.clean_up_logs.expiration_days', $maintenanceSettings['clean_up_logs']['expiration_days']);

        $container->setParameter('social_data.maintenance.fetch_social_posts.enabled', $maintenanceSettings['fetch_social_post']['enabled']);
        $container->setParameter('social_data.maintenance.fetch_social_posts.interval', $maintenanceSettings['fetch_social_post']['interval_in_hours']);
    }

    protected function setupEnvironment(ContainerBuilder $container, array $config): void
    {
        $dataClass = is_string($config['social_post_data_class']) ? $config['social_post_data_class'] : '';

        $environmentServiceDefinition = $container->getDefinition(EnvironmentService::class);
        $environmentServiceDefinition->setArgument('$socialPostDataClass', $dataClass);
    }
}
