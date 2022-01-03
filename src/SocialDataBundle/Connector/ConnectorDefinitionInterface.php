<?php

namespace SocialDataBundle\Connector;

use SocialDataBundle\Model\ConnectorEngineInterface;

interface ConnectorDefinitionInterface
{
    public function setConnectorEngine(?ConnectorEngineInterface $connectorEngine): void;

    public function getConnectorEngine(): ?ConnectorEngineInterface;

    public function setSocialPostBuilder(SocialPostBuilderInterface $builder): void;

    public function getSocialPostBuilder(): SocialPostBuilderInterface;

    /**
     * @throws \Exception
     */
    public function setDefinitionConfiguration(array $definitionConfiguration): void;

    public function engineIsLoaded(): bool;

    /**
     * Returns true if connector is fully configured and ready to provide data.
     */
    public function isOnline(): bool;

    /**
     * @throws \Exception
     */
    public function beforeEnable(): void;

    /**
     * @throws \Exception
     */
    public function beforeDisable(): void;

    public function isAutoConnected(): bool;

    public function isConnected(): bool;

    /**
     * @throws \Exception
     */
    public function connect(): void;

    /**
     * @throws \Exception
     */
    public function disconnect(): void;

    public function getDefinitionConfiguration(): array;

    public function needsEngineConfiguration(): bool;

    public function getEngineConfigurationClass(): ?string;

    public function getFeedConfigurationClass(): string;

    public function getEngineConfiguration(): ?ConnectorEngineConfigurationInterface;
}
