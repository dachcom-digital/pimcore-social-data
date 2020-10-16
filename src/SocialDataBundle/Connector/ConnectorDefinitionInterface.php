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
     * @param SocialPostBuilderInterface $builder
     */
    public function setSocialPostBuilder(SocialPostBuilderInterface $builder);

    /**
     * @return SocialPostBuilderInterface
     */
    public function getSocialPostBuilder();

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
     * @return void
     * @throws \Exception
     */
    public function beforeEnable();

    /**
     * @return void
     * @throws \Exception
     */
    public function beforeDisable();

    /**
     * @return bool
     */
    public function isAutoConnected();

    /**
     * @return bool
     */
    public function isConnected();

    /**
     * @return void
     * @throws \Exception
     */
    public function connect();

    /**
     * @return void
     * @throws \Exception
     */
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
     * @return null|string
     */
    public function getEngineConfigurationClass();

    /**
     * @return string
     */
    public function getFeedConfigurationClass();

    /**
     * @return ConnectorEngineConfigurationInterface|null
     */
    public function getEngineConfiguration();
}
