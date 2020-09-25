<?php

namespace SocialDataBundle\Service;

use SocialDataBundle\Connector\ConnectorDefinitionInterface;
use SocialDataBundle\Connector\ConnectorEngineConfigurationInterface;
use SocialDataBundle\Model\ConnectorEngineInterface;

interface ConnectorServiceInterface
{
    /**
     * @param string $connectorName
     *
     * @return ConnectorEngineInterface
     */
    public function installConnector(string $connectorName);

    /**
     * @param string $connectorName
     */
    public function uninstallConnector(string $connectorName);

    /**
     * @param string $connectorName
     */
    public function enableConnector(string $connectorName);

    /**
     * @param string $connectorName
     */
    public function disableConnector(string $connectorName);

    /**
     * @param string $connectorName
     */
    public function connectConnector(string $connectorName);

    /**
     * @param string $connectorName
     */
    public function disconnectConnector(string $connectorName);


    /**
     * @param string                                $connectorName
     * @param ConnectorEngineConfigurationInterface $connectorConfiguration
     */
    public function updateConnectorEngineConfiguration(string $connectorName, ConnectorEngineConfigurationInterface $connectorConfiguration);

    /**
     * @param string $connectorDefinitionName
     * @param bool   $loadEngine
     *
     * @return ConnectorDefinitionInterface
     */
    public function getConnectorDefinition(string $connectorDefinitionName, bool $loadEngine = false);
}
