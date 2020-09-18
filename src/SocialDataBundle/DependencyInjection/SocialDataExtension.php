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
        }

        foreach (array_merge($this->getCoreConnectors(), $config['available_connectors']) as $availableConnector) {
            $container->setParameter(sprintf('social_data.connectors.system_config.%s', $availableConnector['connector_name']), $availableConnector['connector_config']);
        }

        $container->setParameter('social_data.connectors.available', $availableConnectorsNames);
        $container->setParameter('social_data.logs.expiration_days', $config['log_expiration_days']);

        $this->setupEnvironment($container, $config);
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    protected function setupEnvironment(ContainerBuilder $container, array $config)
    {
        $dataClass = is_string($config['social_post_data_class']) ? $config['social_post_data_class'] : '';

        $connectorServiceDefinition = $container->getDefinition(EnvironmentService::class);
        $connectorServiceDefinition->setArgument('$socialPostDataClass', $dataClass);
    }

    /**
     * @return array
     */
    protected function getCoreConnectors()
    {
        return [
            [
                'connector_name'   => 'facebook',
                'connector_config' => [
                    'core_disabled' => true
                ]
            ]
        ];
    }
}
