<?php

namespace SocialDataBundle\Connector;

interface ConnectorEngineConfigurationInterface
{
    /**
     * @param string $param
     *
     * @return mixed
     */
    public function getConfigParam(string $param);

    /**
     * @return array
     */
    public function toBackendConfigArray();
}
