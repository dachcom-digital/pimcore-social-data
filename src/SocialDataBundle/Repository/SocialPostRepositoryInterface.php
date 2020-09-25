<?php

namespace SocialDataBundle\Repository;

use Pimcore\Model\DataObject\Listing;
use SocialDataBundle\Model\SocialPostInterface;

interface SocialPostRepositoryInterface
{
    /**
     * @param string $socialPostType
     * @param bool   $unpublished
     *
     * @return array|SocialPostInterface[]
     */
    public function findAll(string $socialPostType, bool $unpublished = false): array;

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
    public function findBySocialType(string $socialPostType, bool $unpublished = false): array;

    /**
     * @param int  $wallId
     * @param bool $unpublished
     *
     * @return array
     */
    public function findByWallId(int $wallId, bool $unpublished = false): array;

    /**
     * @param int  $feedId
     * @param bool $unpublished
     *
     * @return array
     */
    public function findByFeedId(int $feedId, bool $unpublished = false): array;

    /**
     * @param string $socialPostType
     * @param int    $wallId
     * @param bool   $unpublished
     *
     * @return array
     */
    public function findBySocialTypeAndWallId(string $socialPostType, int $wallId, bool $unpublished = false): array;

    /**
     * @param string $socialPostType
     * @param int    $wallId
     * @param bool   $unpublished
     *
     * @return array
     */
    public function findBySocialTypeAndFeedId(string $socialPostType, int $wallId, bool $unpublished = false): array;

    /**
     * @param string $socialPostType
     * @param int    $wallId
     * @param int    $feedId
     * @param bool   $unpublished
     *
     * @return array
     */
    public function findBySocialTypeAndWallIdAndFeedId(string $socialPostType, int $wallId, int $feedId, bool $unpublished = false): array;

    /**
     * @return Listing
     */
    public function getList();
}
