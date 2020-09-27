<?php

namespace SocialDataBundle\Dto;

use SocialDataBundle\Connector\ConnectorEngineConfigurationInterface;
use SocialDataBundle\Connector\ConnectorFeedConfigurationInterface;
use SocialDataBundle\Model\FeedInterface;
use SocialDataBundle\Model\WallInterface;

class BuildConfig
{
    /**
     * @var FeedInterface
     */
    protected $feed;

    /**
     * @var ConnectorEngineConfigurationInterface
     */
    protected $engineConfiguration;

    /**
     * @var array
     */
    protected $definitionConfiguration;

    /**
     * @param FeedInterface                         $feed
     * @param ConnectorEngineConfigurationInterface $engineConfiguration
     * @param array                                 $definitionConfiguration
     */
    public function __construct(
        FeedInterface $feed,
        ConnectorEngineConfigurationInterface $engineConfiguration,
        array $definitionConfiguration
    ) {
        $this->feed = $feed;
        $this->engineConfiguration = $engineConfiguration;
        $this->definitionConfiguration = $definitionConfiguration;
    }

    /**
     * @return ConnectorEngineConfigurationInterface
     */
    public function getEngineConfiguration()
    {
        return $this->engineConfiguration;
    }

    /**
     * @return array
     */
    public function getDefinitionConfiguration()
    {
        return $this->definitionConfiguration;
    }

    /**
     * @return WallInterface
     */
    public function getWall()
    {
        return $this->feed->getWall();
    }

    /**
     * @return FeedInterface
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * @return ConnectorFeedConfigurationInterface
     */
    public function getFeedConfiguration()
    {
        return $this->feed->getConfiguration();
    }
}
