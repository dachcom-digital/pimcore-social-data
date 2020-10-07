<?php

namespace SocialDataBundle\Model;

use Doctrine\Common\Collections\Collection;

interface WallInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param string $name
     */
    public function setName(string $name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param array $dataStorage
     */
    public function setDataStorage(array $dataStorage);

    /**
     * @return array
     */
    public function getDataStorage();

    /**
     * @param array $assetStorage
     */
    public function setAssetStorage(array $assetStorage);

    /**
     * @return array
     */
    public function getAssetStorage();

    /**
     * @return \DateTime
     */
    public function getCreationDate();

    /**
     * @param \DateTime $date
     */
    public function setCreationDate(\DateTime $date);

    /**
     * @return bool
     */
    public function hasFeeds();

    /**
     * @param FeedInterface $feed
     *
     * @return bool
     */
    public function hasFeed(FeedInterface $feed);

    /**
     * @param FeedInterface $feed
     */
    public function addFeed(FeedInterface $feed);

    /**
     * @param FeedInterface $feed
     */
    public function removeFeed(FeedInterface $feed);

    /**
     * @return Collection|FeedInterface[]
     */
    public function getFeeds();

    /**
     * @return bool
     */
    public function hasWallTags();

    /**
     * @param TagInterface $wallTag
     *
     * @return bool
     */
    public function hasWallTag(TagInterface $wallTag);

    /**
     * @param TagInterface $wallTag
     */
    public function addWallTag(TagInterface $wallTag);

    /**
     * @param TagInterface $wallTag
     */
    public function removeWallTag(TagInterface $wallTag);

    /**
     * @return TagInterface[]
     */
    public function getWallTags();
}
