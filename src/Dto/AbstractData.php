<?php

namespace SocialDataBundle\Dto;

abstract class AbstractData
{
    protected mixed $transferredData = null;

    public function __construct(
        protected BuildConfig $buildConfig,
        protected array $options
    ) {
    }

    /**
     * @throws \Exception
     */
    public function setTransferredData(mixed $transferredData): void
    {
        if ($this->transferredData !== null) {
            throw new \Exception('Transferred Object already has been set.');
        }

        $this->transferredData = $transferredData;
    }

    public function getBuildConfig(): BuildConfig
    {
        return $this->buildConfig;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
