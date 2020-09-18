<?php

namespace SocialDataBundle\Model;

use SocialDataBundle\Connector\ConnectorEngineConfigurationInterface;

interface ConnectorEngineInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param string $name
     */
    public function setName(string $name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled);

    /**
     * @return bool
     */
    public function getEnabled();

    /**
     * @return bool
     */
    public function isEnabled();

    /**
     * @param ConnectorEngineConfigurationInterface $configuration
     */
    public function setConfiguration(ConnectorEngineConfigurationInterface $configuration);

    /**
     * @return ConnectorEngineConfigurationInterface
     */
    public function getConfiguration();
}
