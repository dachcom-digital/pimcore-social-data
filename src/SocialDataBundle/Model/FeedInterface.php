<?php

namespace SocialDataBundle\Model;

use SocialDataBundle\Connector\ConnectorFeedConfigurationInterface;

interface FeedInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param bool $persistMedia
     */
    public function setPersistMedia(bool $persistMedia);

    /**
     * @return string|null
     */
    public function getPersistMedia();

    /**
     * @param bool $publishPostImmediately
     */
    public function setPublishPostImmediately(bool $publishPostImmediately);

    /**
     * @return bool
     */
    public function getPublishPostImmediately();

    /**
     * @param ConnectorFeedConfigurationInterface|null $configuration
     */
    public function setConfiguration(?ConnectorFeedConfigurationInterface $configuration);

    /**
     * @return ConnectorFeedConfigurationInterface|null
     */
    public function getConfiguration();

    /**
     * @return \DateTime
     */
    public function getCreationDate();

    /**
     * @param \DateTime $date
     */
    public function setCreationDate(\DateTime $date);

    /**
     * @param ConnectorEngineInterface $connectorEngine
     */
    public function setConnectorEngine(ConnectorEngineInterface $connectorEngine);

    /**
     * @return ConnectorEngineInterface
     */
    public function getConnectorEngine();

    /**
     * @param WallInterface $wall
     */
    public function setWall(WallInterface $wall);

    /**
     * @return WallInterface
     */
    public function getWall();
}
