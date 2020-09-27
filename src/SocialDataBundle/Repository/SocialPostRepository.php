<?php

namespace SocialDataBundle\Repository;

use Pimcore\Db\ZendCompatibility\QueryBuilder;
use Pimcore\Model\DataObject\Concrete;
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
    public function findAll(string $socialPostType, bool $unpublished = false)
    {
        return $this->findAllListing($socialPostType, $unpublished)->getObjects();
    }

    /**
     * {@inheritdoc}
     */
    public function findAllListing(string $socialPostType, bool $unpublished = false)
    {
        $listing = $this->getList();
        $listing->setUnpublished($unpublished);
        $listing->addConditionParam('socialType = ?', (string) $socialPostType);

        return $listing;
    }

    /**
     * {@inheritdoc}
     */
    public function findBySocialType(string $socialPostType, bool $unpublished = false)
    {
        return $this->findBySocialTypeListing($socialPostType, $unpublished)->getObjects();
    }

    /**
     * {@inheritdoc}
     */
    public function findBySocialTypeListing(string $socialPostType, bool $unpublished = false)
    {
        $listing = $this->getList();
        $listing->setUnpublished($unpublished);
        $listing->addConditionParam('socialType = ?', (string) $socialPostType);
        $listing->setLimit(1);

        return $listing;
    }

    /**
     * {@inheritdoc}
     */
    public function findByWallId(int $wallId, bool $unpublished = false)
    {
        return $this->findByWallIdListing($wallId, $unpublished)->getObjects();
    }

    /**
     * {@inheritdoc}
     */
    public function findByWallIdListing(int $wallId, bool $unpublished = false)
    {
        $listing = $this->getFeedPostJoinListing($unpublished);
        $listing->addConditionParam('f.wall = ?', (string) $wallId);

        return $listing;
    }

    /**
     * {@inheritdoc}
     */
    public function findByFeedId(int $feedId, bool $unpublished = false): array
    {
        return $this->findByFeedIdListing($feedId, $unpublished)->getObjects();
    }

    /**
     * {@inheritdoc}
     */
    public function findByFeedIdListing(int $feedId, bool $unpublished = false)
    {
        $listing = $this->getFeedPostJoinListing($unpublished);
        $listing->addConditionParam('f.id = ?', (string) $feedId);

        return $listing;
    }

    /**
     * {@inheritdoc}
     */
    public function findBySocialTypeAndWallId(string $socialPostType, int $wallId, bool $unpublished = false)
    {
        return $this->findBySocialTypeAndWallIdListing($socialPostType, $wallId, $unpublished)->getObjects();
    }

    /**
     * {@inheritdoc}
     */
    public function findBySocialTypeAndWallIdListing(string $socialPostType, int $wallId, bool $unpublished = false)
    {
        $listing = $this->getFeedPostJoinListing($unpublished);
        $listing->addConditionParam('f.wall = ?', (string) $wallId);
        $listing->addConditionParam('socialType = ?', $socialPostType);

        return $listing;
    }

    /**
     * {@inheritdoc}
     */
    public function findBySocialTypeAndFeedId(string $socialPostType, int $feedId, bool $unpublished = false)
    {
        return $this->findBySocialTypeAndFeedIdListing($socialPostType, $feedId, $unpublished)->getObjects();
    }

    /**
     * {@inheritdoc}
     */
    public function findBySocialTypeAndFeedIdListing(string $socialPostType, int $feedId, bool $unpublished = false)
    {
        $listing = $this->getFeedPostJoinListing($unpublished);
        $listing->addConditionParam('f.id = ?', (string) $feedId);
        $listing->addConditionParam('socialType = ?', $socialPostType);

        return $listing;
    }

    /**
     * {@inheritdoc}
     */
    public function findBySocialTypeAndWallIdAndFeedId(string $socialPostType, int $wallId, int $feedId, bool $unpublished = false)
    {
        return $this->findBySocialTypeAndWallIdAndFeedIdListing($socialPostType, $wallId, $feedId, $unpublished)->getObjects();
    }

    /**
     * {@inheritdoc}
     */
    public function findBySocialTypeAndWallIdAndFeedIdListing(string $socialPostType, int $wallId, int $feedId, bool $unpublished = false)
    {
        $listing = $this->getFeedPostJoinListing($unpublished);
        $listing->addConditionParam('f.wall = ?', (string) $wallId);
        $listing->addConditionParam('f.id = ?', (string) $feedId);
        $listing->addConditionParam('socialType = ?', $socialPostType);

        return $listing;
    }

    /**
     * {@inheritdoc}
     */
    public function getList()
    {
        $listingClass = sprintf('\Pimcore\Model\DataObject\%s\Listing', ucfirst($this->environmentService->getSocialPostDataClass()));

        return new $listingClass();
    }

    /**
     * {@inheritdoc}
     */
    public function getFeedPostJoinListing(bool $unpublished = false)
    {
        $listing = $this->getList();
        $listing->setUnpublished($unpublished);

        $listing->onCreateQuery(function (QueryBuilder $query) {
            $query->join(
                ['fp' => 'social_data_feed_post'],
                'fp.post_id = o_id',
                ['feedId' => 'fp.feed_id']
            );

            $query->join(
                ['f' => 'social_data_feed'],
                'f.id = fp.feed_id',
                ['wallId' => 'f.wall']
            );
        });

        return $listing;
    }

    /**
     * {@inheritdoc}
     */
    public function getClassId()
    {
        /** @var Concrete $concreteObject */
        $concreteObject = sprintf('\Pimcore\Model\DataObject\%s', ucfirst($this->environmentService->getSocialPostDataClass()));

        return $concreteObject::classId();
    }
}
