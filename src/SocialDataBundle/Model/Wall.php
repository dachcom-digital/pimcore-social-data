<?php

namespace SocialDataBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Wall implements WallInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $dataStorage;

    /**
     * @var array
     */
    protected $assetStorage;

    /**
     * @var \DateTime
     */
    protected $creationDate;

    /**
     * @var Collection|FeedInterface[]
     */
    protected $feeds;

    public function __construct()
    {
        $this->feeds = new ArrayCollection();
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
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setDataStorage(array $dataStorage)
    {
        $this->dataStorage = $dataStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataStorage()
    {
        return $this->dataStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function setAssetStorage(array $assetStorage)
    {
        $this->assetStorage = $assetStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function getAssetStorage()
    {
        return $this->assetStorage;
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
    public function hasFeeds()
    {
        return !$this->feeds->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function hasFeed(FeedInterface $feed)
    {
        return $this->feeds->contains($feed);
    }

    /**
     * {@inheritdoc}
     */
    public function addFeed(FeedInterface $feed)
    {
        if (!$this->hasFeed($feed)) {
            $this->feeds->add($feed);
            $feed->setWall($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeFeed(FeedInterface $feed)
    {
        if ($this->hasFeed($feed)) {
            $this->feeds->removeElement($feed);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFeeds()
    {
        return $this->feeds;
    }
}
