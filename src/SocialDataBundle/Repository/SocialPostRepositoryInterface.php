<?php

namespace SocialDataBundle\Repository;

use Pimcore\Model\DataObject\Listing;
use SocialDataBundle\Model\SocialPostInterface;

interface SocialPostRepositoryInterface
{
    /**
     * @param string $socialPostId
     * @param string $socialPostType
     * @param bool   $unpublished
     *
     * @return SocialPostInterface
     */
    public function findOneByIdAndSocialType(string $socialPostId, string $socialPostType, bool $unpublished = false): ?SocialPostInterface;

    /**
     * @param string $socialPostType
     * @param bool   $unpublished
     *
     * @return array|SocialPostInterface[]
     */
    public function findAll(string $socialPostType, bool $unpublished = false);

    /**
     * @param string $socialPostType
     * @param bool   $unpublished
     *
     * @return Listing
     */
    public function findAllListing(string $socialPostType, bool $unpublished = false);

    /**
     * @param string $socialPostType
     * @param bool   $unpublished
     *
     * @return array|SocialPostInterface[]
     */
    public function findBySocialType(string $socialPostType, bool $unpublished = false);

    /**
     * @param string $socialPostType
     * @param bool   $unpublished
     *
     * @return Listing
     */
    public function findBySocialTypeListing(string $socialPostType, bool $unpublished = false);

    /**
     * @param int  $wallId
     * @param bool $unpublished
     *
     * @return array|SocialPostInterface[]
     */
    public function findByWallId(int $wallId, bool $unpublished = false);#

    /**
     * @param int  $wallId
     * @param bool $unpublished
     *
     * @return Listing
     */
    public function findByWallIdListing(int $wallId, bool $unpublished = false);

    /**
     * @param int  $feedId
     * @param bool $unpublished
     *
     * @return array|SocialPostInterface[]
     */
    public function findByFeedId(int $feedId, bool $unpublished = false);

    /**
     * @param int  $feedId
     * @param bool $unpublished
     *
     * @return Listing
     */
    public function findByFeedIdListing(int $feedId, bool $unpublished = false);

    /**
     * @param string $socialPostType
     * @param int    $wallId
     * @param bool   $unpublished
     *
     * @return array|SocialPostInterface[]
     */
    public function findBySocialTypeAndWallId(string $socialPostType, int $wallId, bool $unpublished = false);

    /**
     * @param string $socialPostType
     * @param int    $wallId
     * @param bool   $unpublished
     *
     * @return Listing
     */
    public function findBySocialTypeAndWallIdListing(string $socialPostType, int $wallId, bool $unpublished = false);

    /**
     * @param string $socialPostType
     * @param int    $feedId
     * @param bool   $unpublished
     *
     * @return array|SocialPostInterface[]
     */
    public function findBySocialTypeAndFeedId(string $socialPostType, int $feedId, bool $unpublished = false);

    /**
     * @param string $socialPostType
     * @param int    $feedId
     * @param bool   $unpublished
     *
     * @return Listing
     */
    public function findBySocialTypeAndFeedIdListing(string $socialPostType, int $feedId, bool $unpublished = false);

    /**
     * @param string $socialPostType
     * @param int    $wallId
     * @param int    $feedId
     * @param bool   $unpublished
     *
     * @return array|SocialPostInterface[]
     */
    public function findBySocialTypeAndWallIdAndFeedId(string $socialPostType, int $wallId, int $feedId, bool $unpublished = false);

    /**
     * @param string $socialPostType
     * @param int    $wallId
     * @param int    $feedId
     * @param bool   $unpublished
     *
     * @return Listing
     */
    public function findBySocialTypeAndWallIdAndFeedIdListing(string $socialPostType, int $wallId, int $feedId, bool $unpublished = false);

    /**
     * @param array $wallTags
     * @param array $feedTags
     *
     * @return array|SocialPostInterface[]
     */
    public function findByTag(array $wallTags = [], array $feedTags = []);

    /**
     * @param array $wallTags
     * @param array $feedTags
     * @param bool  $unpublished
     *
     * @return Listing
     */
    public function findByTagListing(array $wallTags = [], array $feedTags = [], bool $unpublished = false);

    /**
     * @param string $socialPostType
     * @param array  $wallTags
     * @param array  $feedTags
     *
     * @return array|SocialPostInterface[]
     */
    public function findSocialTypeAndByTag(string $socialPostType, array $wallTags = [], array $feedTags = []);

    /**
     * @param string $socialPostType
     * @param array  $wallTags
     * @param array  $feedTags
     * @param bool   $unpublished
     *
     * @return Listing
     */
    public function findSocialTypeAndByTagListing(string $socialPostType, array $wallTags = [], array $feedTags = [], bool $unpublished = false);

    /**
     * @return Listing
     */
    public function getList();

    /**
     * @param bool $unpublished
     *
     * @param bool $joinWallTagTables
     * @param bool $joinFeedTagTables
     *
     * @return Listing
     */
    public function getFeedPostJoinListing(bool $unpublished = false, bool $joinWallTagTables = false, bool $joinFeedTagTables = false);

    /**
     * @return int
     */
    public function getClassId();
}
