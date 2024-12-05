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

namespace SocialDataBundle\Repository;

use Pimcore\Model\DataObject\Listing;
use SocialDataBundle\Model\SocialPostInterface;

interface SocialPostRepositoryInterface
{
    public function findOneByIdAndSocialType(string $socialPostId, string $socialPostType, bool $unpublished = false): ?SocialPostInterface;

    /**
     * @return array<int, SocialPostInterface>
     */
    public function findAll(string $socialPostType, bool $unpublished = false): array;

    public function findAllListing(string $socialPostType, bool $unpublished = false): Listing;

    /**
     * @return array<int, SocialPostInterface>
     */
    public function findBySocialType(string $socialPostType, bool $unpublished = false): array;

    public function findBySocialTypeListing(string $socialPostType, bool $unpublished = false): Listing;

    /**
     * @return array<int, SocialPostInterface>
     */
    public function findByWallId(int $wallId, bool $unpublished = false): array;

    public function findByWallIdListing(int $wallId, bool $unpublished = false): Listing;

    /**
     * @return array<int, SocialPostInterface>
     */
    public function findByFeedId(int $feedId, bool $unpublished = false): array;

    public function findByFeedIdListing(int $feedId, bool $unpublished = false): Listing;

    /**
     * @return array<int, SocialPostInterface>
     */
    public function findBySocialTypeAndWallId(string $socialPostType, int $wallId, bool $unpublished = false): array;

    public function findBySocialTypeAndWallIdListing(string $socialPostType, int $wallId, bool $unpublished = false): Listing;

    /**
     * @return array<int, SocialPostInterface>
     */
    public function findBySocialTypeAndFeedId(string $socialPostType, int $feedId, bool $unpublished = false): array;

    public function findBySocialTypeAndFeedIdListing(string $socialPostType, int $feedId, bool $unpublished = false): Listing;

    /**
     * @return array<int, SocialPostInterface>
     */
    public function findBySocialTypeAndWallIdAndFeedId(string $socialPostType, int $wallId, int $feedId, bool $unpublished = false): array;

    public function findBySocialTypeAndWallIdAndFeedIdListing(string $socialPostType, int $wallId, int $feedId, bool $unpublished = false): Listing;

    /**
     * @return array<int, SocialPostInterface>
     */
    public function findByTag(array $wallTags = [], array $feedTags = []): array;

    public function findByTagListing(array $wallTags = [], array $feedTags = [], bool $unpublished = false): Listing;

    /**
     * @return array<int, SocialPostInterface>
     */
    public function findSocialTypeAndByTag(string $socialPostType, array $wallTags = [], array $feedTags = []): array;

    public function findSocialTypeAndByTagListing(string $socialPostType, array $wallTags = [], array $feedTags = [], bool $unpublished = false): Listing;

    public function getList(): Listing;

    public function getFeedPostJoinListing(bool $unpublished = false, bool $joinWallTagTables = false, bool $joinFeedTagTables = false): Listing;

    public function deleteOutdatedSocialPosts(int $expireDays, bool $deletePoster = false): void;

    public function getClassId(): string;
}
