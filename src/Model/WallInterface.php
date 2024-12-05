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
