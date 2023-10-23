<?php

namespace SocialDataBundle\Model;

use SocialDataBundle\Connector\ConnectorEngineConfigurationInterface;

interface ConnectorEngineInterface
{
    public function getId(): int;

    public function setName(string $name): void;

    public function getName(): string;

    public function setEnabled(bool $enabled): void;

    public function getEnabled(): bool;

    public function isEnabled(): bool;

    public function setConfiguration(ConnectorEngineConfigurationInterface $configuration): void;

    public function getConfiguration(): ?ConnectorEngineConfigurationInterface;
}
