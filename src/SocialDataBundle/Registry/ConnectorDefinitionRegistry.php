<?php

namespace SocialDataBundle\Registry;

use SocialDataBundle\Connector\ConnectorDefinitionInterface;

class ConnectorDefinitionRegistry implements ConnectorDefinitionRegistryInterface
{
    protected array $connector = [];

    public function register(mixed $service, string $identifier): void
    {
        if (!in_array(ConnectorDefinitionInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), ConnectorDefinitionInterface::class, implode(', ', class_implements($service)))
            );
        }

        $this->connector[$identifier] = $service;
    }

    public function has(string $identifier): bool
    {
        return isset($this->connector[$identifier]);
    }

    public function get($identifier): ConnectorDefinitionInterface
    {
        if (!$this->has($identifier)) {
            throw new \Exception('"' . $identifier . '" Connector does not exist');
        }

        return $this->connector[$identifier];
    }

    public function getAll(): array
    {
        return $this->connector;
    }

    public function getAllIdentifier(): array
    {
        return array_keys($this->connector);
    }
}
