<?php

namespace SocialDataBundle\Connector;

use SocialDataBundle\Model\ConnectorEngineInterface;

interface ConnectorDefinitionInterface
{
    /**
     * @param ConnectorEngineInterface|null $connectorEngine
     */
    public function setConnectorEngine(?ConnectorEngineInterface $connectorEngine);

    /**
     * @return ConnectorEngineInterface|null
     */
    public function getConnectorEngine();

    /**
     * @param array $configuration
     *
     * @throws \Exception
     */
    public function setDefinitionConfiguration(array $configuration);

    /**
     * @return bool
     */
    public function engineIsLoaded();

    /**
     * Returns true if connector is fully configured and ready to provide data.
     *
     * @return bool
     */
    public function isOnline();

    /**
     * @throws \Exception
     */
    public function beforeEnable();

    /**
     * @throws \Exception
     */
    public function beforeDisable();

    /**
     * @return bool
     */
    public function allowMultipleContextItems();

    /**
     * @return bool
     */
    public function isAutoConnected();

    /**
     * @return bool
     */
    public function isConnected();

    public function connect();

    public function disconnect();

    /**
     * @return array
     */
    public function getDefinitionConfiguration();

    /**
     * @return bool
     */
    public function needsEngineConfiguration();

    /**
     * @return bool
     */
    public function hasLogPanel();

    /**
     * @return null|string
     */
    public function getEngineConfigurationClass();

    /**
     * @return array|null
     */
    public function getEngineConfiguration();

    /**
     * @param array $data
     *
     * @return ConnectorEngineConfigurationInterface|null
     */
    public function mapEngineConfigurationFromBackend(array $data);
}
