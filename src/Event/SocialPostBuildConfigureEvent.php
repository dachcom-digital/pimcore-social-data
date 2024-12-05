<?php

namespace SocialDataBundle\Event;

use SocialDataBundle\Dto\BuildConfig;
use Symfony\Contracts\EventDispatcher\Event;

class SocialPostBuildConfigureEvent extends Event
{
    protected array $options;
    protected string $connectorName;
    protected BuildConfig $buildConfig;

    public function __construct(string $connectorName, BuildConfig $buildConfig)
    {
        $this->options = [];
        $this->connectorName = $connectorName;
        $this->buildConfig = $buildConfig;
    }

    public function getConnectorName(): string
    {
        return $this->connectorName;
    }

    public function getBuildConfig(): BuildConfig
    {
        return $this->buildConfig;
    }

    public function setOption(mixed $key, mixed $value): void
    {
        $this->options[$key] = $value;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
