<?php

namespace SocialDataBundle\DependencyInjection;

use SocialDataBundle\Service\EnvironmentService;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class SocialDataExtension extends Extension
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator([__DIR__ . '/../Resources/config']));
        $loader->load('services.yml');

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

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    protected function setupMaintenanceSetting(ContainerBuilder $container, array $config)
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

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    protected function setupEnvironment(ContainerBuilder $container, array $config)
    {
        $dataClass = is_string($config['social_post_data_class']) ? $config['social_post_data_class'] : '';

        $environmentServiceDefinition = $container->getDefinition(EnvironmentService::class);
        $environmentServiceDefinition->setArgument('$socialPostDataClass', $dataClass);
    }
}
