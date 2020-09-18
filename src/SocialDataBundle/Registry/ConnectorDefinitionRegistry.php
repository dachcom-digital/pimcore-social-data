<?php

namespace SocialDataBundle\Registry;

use SocialDataBundle\Connector\ConnectorDefinitionInterface;

class ConnectorDefinitionRegistry implements ConnectorDefinitionRegistryInterface
{
    /**
     * @var array
     */
    protected $connector;

    /**
     * @param ConnectorDefinitionInterface $service
     * @param string                       $identifier
     */
    public function register($service, $identifier)
    {
        if (!in_array(ConnectorDefinitionInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), ConnectorDefinitionInterface::class, implode(', ', class_implements($service)))
            );
        }

        $this->connector[$identifier] = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function has($identifier)
    {
        return isset($this->connector[$identifier]);
    }

    /**
     * {@inheritdoc}
     */
    public function get($identifier)
    {
        if (!$this->has($identifier)) {
            throw new \Exception('"' . $identifier . '" Connector does not exist');
        }

        return $this->connector[$identifier];
    }

    /**
     * {@inheritdoc}
     */
    public function getAll()
    {
        return $this->connector;
    }
}
