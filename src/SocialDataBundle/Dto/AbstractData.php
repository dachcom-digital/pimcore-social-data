<?php

namespace SocialDataBundle\Dto;

abstract class AbstractData
{
    /**
     * @var BuildConfig
     */
    protected $buildConfig;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var mixed
     */
    protected $transferredData;

    /**
     * @param BuildConfig $buildConfig
     * @param array       $options
     */
    public function __construct(BuildConfig $buildConfig, array $options)
    {
        $this->buildConfig = $buildConfig;
        $this->options = $options;
    }

    /**
     * @param mixed $transferredData
     *
     * @throws \Exception
     */
    public function setTransferredData($transferredData)
    {
        if ($this->transferredData !== null) {
            throw new \Exception('Transferred Object already has been set.');
        }

        $this->transferredData = $transferredData;
    }

    /**
     * @return BuildConfig
     */
    public function getBuildConfig()
    {
        return $this->buildConfig;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
