<?php

namespace SocialDataBundle\Dto;

use SocialDataBundle\Connector\ConnectorEngineConfigurationInterface;
use SocialDataBundle\Connector\ConnectorFeedConfigurationInterface;
use SocialDataBundle\Model\FeedInterface;
use SocialDataBundle\Model\WallInterface;

class BuildConfig
{
    public function __construct(
        protected FeedInterface $feed,
        protected ConnectorEngineConfigurationInterface $engineConfiguration,
        protected array $definitionConfiguration
    ) {
    }

    public function getEngineConfiguration(): ConnectorEngineConfigurationInterface
    {
        return $this->engineConfiguration;
    }

    public function getDefinitionConfiguration(): array
    {
        return $this->definitionConfiguration;
    }

    public function getWall(): WallInterface
    {
        return $this->feed->getWall();
    }

    public function getFeed(): FeedInterface
    {
        return $this->feed;
    }

    public function getFeedConfiguration(): ConnectorFeedConfigurationInterface
    {
        return $this->feed->getConfiguration();
    }
}
