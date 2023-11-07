<?php

namespace SocialDataBundle\Registry;

use SocialDataBundle\Connector\ConnectorDefinitionInterface;

interface ConnectorDefinitionRegistryInterface
{
    public function has(string $identifier): bool;

    /**
     * @throws \Exception
     */
    public function get(string $identifier): ConnectorDefinitionInterface;

    /**
     * @return array<int, ConnectorDefinitionInterface>
     */
    public function getAll(): array;

    public function getAllIdentifier(): array;
}
