<?php

namespace SocialDataBundle\Event;

use SocialDataBundle\Dto\BuildConfig;
use Symfony\Contracts\EventDispatcher\Event;

class SocialPostBuildConfigureEvent extends Event
{
    /**
     * @var string
     */
    protected $connectorName;

    /**
     * @var BuildConfig
     */
    protected $buildConfig;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param string      $connectorName
     * @param BuildConfig $buildConfig
     */
    public function __construct(string $connectorName, BuildConfig $buildConfig)
    {
        $this->options = [];
        $this->connectorName = $connectorName;
        $this->buildConfig = $buildConfig;
    }

    /**
     * @return string
     */
    public function getConnectorName()
    {
        return $this->connectorName;
    }

    /**
     * @return BuildConfig
     */
    public function getBuildConfig()
    {
        return $this->buildConfig;
    }

    /**
     * @param mixed $key
     * @param mixed $value
     *
     * @throws \Exception
     */
    public function setOption($key, $value)
    {
        $this->options[$key] = $value;
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }
}
