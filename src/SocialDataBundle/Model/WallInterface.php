<?php

namespace SocialDataBundle\Model;

interface WallInterface
{
    public function getId(): int;

    public function setName(string $name): void;

    public function getName(): string;

    public function setDataStorage(array $dataStorage): void;

    public function getDataStorage(): ?array;

    public function setAssetStorage(array $assetStorage): void;

    public function getAssetStorage(): ?array;

    public function getCreationDate(): \DateTime;

    public function setCreationDate(\DateTime $date): void;

    public function hasFeeds(): bool;

    public function hasFeed(FeedInterface $feed): bool;

    public function addFeed(FeedInterface $feed): void;

    public function removeFeed(FeedInterface $feed): void;

    /**
     * @return array<int, FeedInterface>
     */
    public function getFeeds(): iterable;

    public function hasWallTags(): bool;

    public function hasWallTag(TagInterface $wallTag): bool;

    public function addWallTag(TagInterface $wallTag): void;

    public function removeWallTag(TagInterface $wallTag): void;

    /**
     * @return array<int, TagInterface>
     */
    public function getWallTags();
}
