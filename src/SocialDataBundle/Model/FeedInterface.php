<?php

namespace SocialDataBundle\Model;

use SocialDataBundle\Connector\ConnectorFeedConfigurationInterface;

interface FeedInterface
{
    public function getId(): int;

    public function setPersistMedia(bool $persistMedia): void;

    public function getPersistMedia(): bool;

    public function setPublishPostImmediately(bool $publishPostImmediately): void;

    public function getPublishPostImmediately(): bool;

    public function setConfiguration(?ConnectorFeedConfigurationInterface $configuration): void;

    public function getConfiguration(): ?ConnectorFeedConfigurationInterface;

    public function getCreationDate(): \DateTime;

    public function setCreationDate(\DateTime $date): void;

    public function setConnectorEngine(ConnectorEngineInterface $connectorEngine): void;

    public function getConnectorEngine(): ConnectorEngineInterface;

    public function setWall(WallInterface $wall): void;

    public function getWall();

    public function hasFeedTags(): bool;

    public function hasFeedTag(TagInterface $feedTag): bool;

    public function addFeedTag(TagInterface $feedTag): void;

    public function removeFeedTag(TagInterface $feedTag): void;

    /**
     * @return array<int, TagInterface>
     */
    public function getFeedTags(): iterable;
}
