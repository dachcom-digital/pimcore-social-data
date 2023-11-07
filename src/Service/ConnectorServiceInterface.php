<?php

namespace SocialDataBundle\Service;

use SocialDataBundle\Connector\ConnectorDefinitionInterface;
use SocialDataBundle\Connector\ConnectorEngineConfigurationInterface;
use SocialDataBundle\Model\ConnectorEngineInterface;

interface ConnectorServiceInterface
{
    public function installConnector(string $connectorName): ConnectorEngineInterface;

    public function uninstallConnector(string $connectorName): void;

    public function enableConnector(string $connectorName): void;

    public function disableConnector(string $connectorName): void;

    public function connectConnector(string $connectorName): void;

    public function disconnectConnector(string $connectorName): void;

    public function updateConnectorEngineConfiguration(string $connectorName, ConnectorEngineConfigurationInterface $connectorConfiguration): void;

    public function getConnectorDefinition(string $connectorDefinitionName, bool $loadEngine = false): ConnectorDefinitionInterface;
}
