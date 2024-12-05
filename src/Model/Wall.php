<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

namespace SocialDataBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Wall implements WallInterface
{
    protected int $id;
    protected string $name;
    protected ?array $dataStorage = null;
    protected ?array $assetStorage = null;
    protected \DateTime $creationDate;
    protected Collection $feeds;
    protected Collection $wallTags;

    public function __construct()
    {
        $this->feeds = new ArrayCollection();
        $this->wallTags = new ArrayCollection();
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setDataStorage(?array $dataStorage): void
    {
        $this->dataStorage = $dataStorage;
    }

    public function getDataStorage(): ?array
    {
        return $this->dataStorage;
    }

    public function setAssetStorage(array $assetStorage): void
    {
        $this->assetStorage = $assetStorage;
    }

    public function getAssetStorage(): ?array
    {
        return $this->assetStorage;
    }

    public function setCreationDate(\DateTime $date): void
    {
        $this->creationDate = $date;
    }

    public function getCreationDate(): \DateTime
    {
        return $this->creationDate;
    }

    public function hasFeeds(): bool
    {
        return !$this->feeds->isEmpty();
    }

    public function hasFeed(FeedInterface $feed): bool
    {
        return $this->feeds->contains($feed);
    }

    public function addFeed(FeedInterface $feed): void
    {
        if (!$this->hasFeed($feed)) {
            $this->feeds->add($feed);
            $feed->setWall($this);
        }
    }

    public function removeFeed(FeedInterface $feed): void
    {
        if ($this->hasFeed($feed)) {
            $this->feeds->removeElement($feed);
        }
    }

    public function getFeeds(): iterable
    {
        return $this->feeds;
    }

    public function hasWallTags(): bool
    {
        return !$this->wallTags->isEmpty();
    }

    public function hasWallTag(TagInterface $wallTag): bool
    {
        return $this->wallTags->contains($wallTag);
    }

    public function addWallTag(TagInterface $wallTag): void
    {
        if (!$this->hasWallTag($wallTag)) {
            $this->wallTags->add($wallTag);
        }
    }

    public function removeWallTag(TagInterface $wallTag): void
    {
        if ($this->hasWallTag($wallTag)) {
            $this->wallTags->removeElement($wallTag);
        }
    }

    public function getWallTags(): iterable
    {
        return $this->wallTags;
    }
}
