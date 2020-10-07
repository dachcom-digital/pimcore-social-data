<?php

namespace SocialDataBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use SocialDataBundle\Connector\ConnectorFeedConfigurationInterface;

class Feed implements FeedInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var bool
     */
    protected $persistMedia;

    /**
     * @var bool
     */
    protected $publishPostImmediately;

    /**
     * @var ConnectorFeedConfigurationInterface|null
     */
    protected $configuration;

    /**
     * @var \DateTime
     */
    protected $creationDate;

    /**
     * @var ConnectorEngineInterface
     */
    protected $connectorEngine;

    /**
     * @var WallInterface
     */
    protected $wall;

    /**
     * @var Collection|FeedInterface[]
     */
    protected $feedTags;

    public function __construct()
    {
        $this->feedTags = new ArrayCollection();

        if ($this->getCreationDate() === null) {
            $this->setCreationDate(new \DateTime('now'));
        }
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setPersistMedia(bool $persistMedia)
    {
        $this->persistMedia = $persistMedia;
    }

    /**
     * {@inheritdoc}
     */
    public function getPersistMedia()
    {
        return $this->persistMedia;
    }

    /**
     * {@inheritdoc}
     */
    public function setPublishPostImmediately(bool $publishPostImmediately)
    {
        $this->publishPostImmediately = $publishPostImmediately;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishPostImmediately()
    {
        return $this->publishPostImmediately;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration(?ConnectorFeedConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreationDate(\DateTime $date)
    {
        $this->creationDate = $date;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * {@inheritdoc}
     */
    public function setConnectorEngine(ConnectorEngineInterface $connectorEngine)
    {
        $this->connectorEngine = $connectorEngine;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnectorEngine()
    {
        return $this->connectorEngine;
    }

    /**
     * {@inheritdoc}
     */
    public function setWall(WallInterface $wall)
    {
        $this->wall = $wall;
    }

    /**
     * {@inheritdoc}
     */
    public function getWall()
    {
        return $this->wall;
    }

    /**
     * {@inheritdoc}
     */
    public function hasFeedTags()
    {
        return !$this->feedTags->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function hasFeedTag(TagInterface $feedTag)
    {
        return $this->feedTags->contains($feedTag);
    }

    /**
     * {@inheritdoc}
     */
    public function addFeedTag(TagInterface $feedTag)
    {
        if (!$this->hasFeedTag($feedTag)) {
            $this->feedTags->add($feedTag);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeFeedTag(TagInterface $feedTag)
    {
        if ($this->hasFeedTag($feedTag)) {
            $this->feedTags->removeElement($feedTag);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFeedTags()
    {
        return $this->feedTags;
    }
}
