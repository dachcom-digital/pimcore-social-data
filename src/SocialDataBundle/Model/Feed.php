<?php

namespace SocialDataBundle\Model;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use SocialDataBundle\Connector\ConnectorFeedConfigurationInterface;

class Feed implements FeedInterface
{
    protected int $id;
    protected bool $persistMedia;
    protected bool $publishPostImmediately;
    protected ?ConnectorFeedConfigurationInterface $configuration;
    protected \DateTime $creationDate;
    protected ConnectorEngineInterface $connectorEngine;
    protected WallInterface $wall;
    protected Collection $feedTags;

    public function __construct()
    {
        $this->feedTags = new ArrayCollection();

        if ($this->getCreationDate() === null) {
            $this->setCreationDate(new \DateTime('now'));
        }
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setPersistMedia(bool $persistMedia): void
    {
        $this->persistMedia = $persistMedia;
    }

    public function getPersistMedia(): bool
    {
        return $this->persistMedia;
    }

    public function setPublishPostImmediately(bool $publishPostImmediately): void
    {
        $this->publishPostImmediately = $publishPostImmediately;
    }

    public function getPublishPostImmediately(): bool
    {
        return $this->publishPostImmediately;
    }

    public function setConfiguration(?ConnectorFeedConfigurationInterface $configuration): void
    {
        $this->configuration = $configuration;
    }

    public function getConfiguration(): ?ConnectorFeedConfigurationInterface
    {
        return $this->configuration;
    }

    public function setCreationDate(\DateTime $date): void
    {
        $this->creationDate = $date;
    }

    public function getCreationDate(): \DateTime
    {
        return $this->creationDate;
    }

    public function setConnectorEngine(ConnectorEngineInterface $connectorEngine): void
    {
        $this->connectorEngine = $connectorEngine;
    }

    public function getConnectorEngine(): ConnectorEngineInterface
    {
        return $this->connectorEngine;
    }

    public function setWall(WallInterface $wall): void
    {
        $this->wall = $wall;
    }

    public function getWall(): WallInterface
    {
        return $this->wall;
    }

    public function hasFeedTags(): bool
    {
        return !$this->feedTags->isEmpty();
    }

    public function hasFeedTag(TagInterface $feedTag): bool
    {
        return $this->feedTags->contains($feedTag);
    }

    public function addFeedTag(TagInterface $feedTag): void
    {
        if (!$this->hasFeedTag($feedTag)) {
            $this->feedTags->add($feedTag);
        }
    }

    public function removeFeedTag(TagInterface $feedTag): void
    {
        if ($this->hasFeedTag($feedTag)) {
            $this->feedTags->removeElement($feedTag);
        }
    }

    public function getFeedTags(): iterable
    {
        return $this->feedTags;
    }
}
