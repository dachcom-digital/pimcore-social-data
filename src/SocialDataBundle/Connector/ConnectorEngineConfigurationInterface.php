<?php

namespace SocialDataBundle\Connector;

interface ConnectorEngineConfigurationInterface
{
    /**
     * @return string
     */
    public static function getFormClass();
}
