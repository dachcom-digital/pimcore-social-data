<?php

namespace SocialDataBundle\Repository;

use Pimcore\Model\DataObject\Listing;
use SocialDataBundle\Model\SocialPostInterface;
use SocialDataBundle\Service\EnvironmentService;

class SocialPostRepository implements SocialPostRepositoryInterface
{
    /**
     * @var EnvironmentService
     */
    protected $environmentService;

    /**
     * @param EnvironmentService $environmentService
     */
    public function __construct(EnvironmentService $environmentService)
    {
        $this->environmentService = $environmentService;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(string $socialPostType, bool $unpublished = false): array
    {
        $listing = $this->getList();
        $listing->setUnpublished($unpublished);
        $listing->addConditionParam('socialType = ?', (string) $socialPostType);

        return $listing->getObjects();
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByIdAndSocialType(string $socialPostId, string $socialPostType, bool $unpublished = false): ?SocialPostInterface
    {
        $listing = $this->getList();
        $listing->setUnpublished($unpublished);
        $listing->addConditionParam('socialType = ?', (string) $socialPostType);
        $listing->addConditionParam('socialId = ?', (string) $socialPostId);
        $listing->setLimit(1);

        $objects = $listing->getObjects();

        return count($objects) === 0 ? null : $objects[0];
    }

    /**
     * {@inheritdoc}
     */
    public function findBySocialType(string $socialPostType, bool $unpublished = false): array
    {
        $listing = $this->getList();
        $listing->setUnpublished($unpublished);
        $listing->addConditionParam('socialType = ?', (string) $socialPostType);
        $listing->setLimit(1);

        return $listing->getObjects();
    }

    /**
     * {@inheritdoc}
     */
    public function findByWallId(int $wallId, bool $unpublished = false): array
    {
        // @todo!

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function findByFeedId(int $feedId, bool $unpublished = false): array
    {
        // @todo!

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function findBySocialTypeAndWallId(string $socialPostType, int $wallId, bool $unpublished = false): array
    {
        // @todo!

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function findBySocialTypeAndFeedId(string $socialPostType, int $wallId, bool $unpublished = false): array
    {
        // @todo!

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function findBySocialTypeAndWallIdAndFeedId(string $socialPostType, int $wallId, int $feedId, bool $unpublished = false): array
    {
       // @todo!

        return [];
    }

    /**
     * @return Listing
     */
    public function getList()
    {
        $listingClass = sprintf('\Pimcore\Model\DataObject\%s\Listing', ucfirst($this->environmentService->getSocialPostDataClass()));

        return new $listingClass();
    }
}
