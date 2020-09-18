<?php

namespace SocialDataBundle\Connector;

use SocialDataBundle\Manager\ConnectorManagerInterface;
use SocialDataBundle\Model\ConnectorEngineInterface;
use SocialDataBundle\Registry\ConnectorDefinitionRegistryInterface;

class ConnectorService implements ConnectorServiceInterface
{
    /**
     * @var array|ConnectorEngineInterface[]
     */
    protected $connectorCache = [];

    /**
     * @var ConnectorDefinitionRegistryInterface
     */
    protected $connectorDefinitionRegistry;

    /**
     * @var ConnectorManagerInterface
     */
    protected $connectorManager;

    /**
     * @param ConnectorDefinitionRegistryInterface $connectorDefinitionRegistry
     * @param ConnectorManagerInterface            $connectorManager
     */
    public function __construct(
        ConnectorDefinitionRegistryInterface $connectorDefinitionRegistry,
        ConnectorManagerInterface $connectorManager
    ) {
        $this->connectorCache = [];
        $this->connectorDefinitionRegistry = $connectorDefinitionRegistry;
        $this->connectorManager = $connectorManager;
    }

    /**
     * {@inheritdoc}
     */
    public function installConnector(string $connectorName)
    {
        $connectorDefinition = $this->getConnectorDefinition($connectorName, true);
        if ($connectorDefinition->engineIsLoaded()) {
            throw new \Exception(sprintf('Cannot install "%s". Connector already exists.', $connectorName));
        }

        return $this->connectorManager->createNewEngine($connectorName);
    }

    /**
     * {@inheritdoc}
     */
    public function uninstallConnector(string $connectorName)
    {
        $connectorDefinition = $this->getConnectorDefinition($connectorName, true);
        if (!$connectorDefinition->engineIsLoaded()) {
            throw new \Exception(sprintf('Cannot uninstall "%s". Connector does not exist.', $connectorName));
        }

        if ($connectorDefinition->getConnectorEngine()->isEnabled() === true) {
            throw new \Exception(sprintf('Cannot uninstall "%s". Connector is currently enabled.', $connectorName));
        }

        $this->connectorManager->deleteEngineByName($connectorName);
    }

    /**
     * {@inheritdoc}
     */
    public function enableConnector(string $connectorName)
    {
        $connectorDefinition = $this->getConnectorDefinition($connectorName, true);

        if (!$connectorDefinition->engineIsLoaded()) {
            throw new \Exception(sprintf('Cannot enable "%s". Connector does not exist.', $connectorName));
        }

        $connectorEngine = $connectorDefinition->getConnectorEngine();
        if ($connectorEngine->isEnabled() === true) {
            throw new \Exception(sprintf('Cannot enable "%s". Connector already enabled.', $connectorName));
        }

        try {
            $connectorDefinition->beforeEnable();
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Cannot enable "%s". %s.', $connectorName, $e->getMessage()));
        }

        $connectorEngine->setEnabled(true);

        $this->connectorManager->updateEngine($connectorEngine);
    }

    /**
     * {@inheritdoc}
     */
    public function disableConnector(string $connectorName)
    {
        $connectorDefinition = $this->getConnectorDefinition($connectorName, true);

        if (!$connectorDefinition->engineIsLoaded()) {
            throw new \Exception(sprintf('Cannot disable "%s". Connector does not exist.', $connectorName));
        }

        $connectorEngine = $connectorDefinition->getConnectorEngine();
        if ($connectorEngine->isEnabled() === false) {
            throw new \Exception(sprintf('Cannot disable "%s". Connector already disabled.', $connectorName));
        }

        try {
            $connectorDefinition->beforeDisable();
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Cannot disable "%s". %s.', $connectorName, $e->getMessage()));
        }

        $connectorEngine->setEnabled(false);

        $this->connectorManager->updateEngine($connectorEngine);
    }

    /**
     * {@inheritdoc}
     */
    public function connectConnector(string $connectorName)
    {
        $connectorDefinition = $this->getConnectorDefinition($connectorName, true);

        if (!$connectorDefinition->engineIsLoaded()) {
            throw new \Exception(sprintf('Cannot connect "%s". Connector does not exist.', $connectorName));
        }

        if (!$connectorDefinition->getConnectorEngine()->isEnabled()) {
            throw new \Exception(sprintf('Cannot connect  "%s". Connector is not enabled.', $connectorName));
        }

        try {
            $connectorDefinition->connect();
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Cannot connect "%s". %s.', $connectorName, $e->getMessage()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function disconnectConnector(string $connectorName)
    {
        $connectorDefinition = $this->getConnectorDefinition($connectorName, true);

        if (!$connectorDefinition->engineIsLoaded()) {
            throw new \Exception(sprintf('Cannot disconnect "%s". Connector does not exist.', $connectorName));
        }

        if (!$connectorDefinition->getConnectorEngine()->isEnabled()) {
            throw new \Exception(sprintf('Cannot disconnect  "%s". Connector is not enabled.', $connectorName));
        }

        try {
            $connectorDefinition->disconnect();
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Cannot disconnect "%s". %s.', $connectorName, $e->getMessage()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updateConnectorEngineConfiguration(string $connectorName, ConnectorEngineConfigurationInterface $connectorConfiguration)
    {
        $connectorDefinition = $this->getConnectorDefinition($connectorName, true);

        if (!$connectorDefinition->engineIsLoaded()) {
            throw new \Exception(sprintf('Cannot fetch configuration for "%s". Connector Engine is not loaded.', $connectorName));
        }

        $connectorEngine = $connectorDefinition->getConnectorEngine();
        $connectorEngine->setConfiguration(clone $connectorConfiguration);
        $this->connectorManager->updateEngine($connectorEngine);
    }

    /**
     * {@inheritdoc}
     */
    public function connectorDefinitionIsEnabled(string $connectorDefinition)
    {
        return $this->connectorManager->connectorDefinitionIsEnabled($connectorDefinition);
    }

    /**
     * {@inheritdoc}
     */
    public function getConnectorDefinition(string $connectorName, bool $loadEngine = false)
    {
        return $this->connectorManager->getConnectorDefinition($connectorName, $loadEngine);
    }
}
