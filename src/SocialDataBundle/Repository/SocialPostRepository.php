<?php

namespace SocialDataBundle\Repository;

use Carbon\Carbon;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Db\ZendCompatibility\QueryBuilder;
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
    public function findByTag(array $wallTags = [], array $feedTags = [], bool $unpublished = false)
    {
        return $this->findByTagListing($wallTags, $feedTags, $unpublished)->getObjects();
    }

    /**
     * {@inheritdoc}
     */
    public function findByTagListing(array $wallTags = [], array $feedTags = [], bool $unpublished = false)
    {
        $joinWallTagTables = count($wallTags) > 0;
        $joinFeedTagTables = count($feedTags) > 0;

        $listing = $this->getFeedPostJoinListing($unpublished, $joinWallTagTables, $joinFeedTagTables);

        if ($joinWallTagTables === true) {
            $listing->addConditionParam(sprintf('wtt.name IN (%s)', sprintf('"%s"', implode('","', $wallTags))));
        }

        if ($joinFeedTagTables === true) {
            $listing->addConditionParam(sprintf('ftt.name IN (%s)', sprintf('"%s"', implode('","', $feedTags))));
        }

        return $listing;
    }

    /**
     * {@inheritdoc}
     */
    public function findSocialTypeAndByTag(string $socialPostType, array $wallTags = [], array $feedTags = [], bool $unpublished = false)
    {
        return $this->findSocialTypeAndByTagListing($socialPostType, $wallTags, $feedTags, $unpublished)->getObjects();
    }

    /**
     * {@inheritdoc}
     */
    public function findSocialTypeAndByTagListing(string $socialPostType, array $wallTags = [], array $feedTags = [], bool $unpublished = false)
    {
        $joinWallTagTables = count($wallTags) > 0;
        $joinFeedTagTables = count($feedTags) > 0;

        $listing = $this->getFeedPostJoinListing($unpublished, $joinWallTagTables, $joinFeedTagTables);
        $listing->addConditionParam('socialType = ?', $socialPostType);

        if ($joinWallTagTables === true) {
            $listing->addConditionParam(sprintf('wtt.name IN (%s)', sprintf('"%s"', implode('","', $wallTags))));
        }

        if ($joinFeedTagTables === true) {
            $listing->addConditionParam(sprintf('ftt.name IN (%s)', sprintf('"%s"', implode('","', $feedTags))));
        }

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
    public function getFeedPostJoinListing(bool $unpublished = false, bool $joinWallTagTables = false, bool $joinFeedTagTables = false)
    {
        $listing = $this->getList();
        $listing->setUnpublished($unpublished);

        $listing->onCreateQuery(function (QueryBuilder $query) use ($joinWallTagTables, $joinFeedTagTables) {

            $query
                ->join(['fp' => 'social_data_feed_post'], 'fp.post_id = o_id', ['feedId' => 'fp.feed_id'])
                ->join(['f' => 'social_data_feed'], 'f.id = fp.feed_id', ['wallId' => 'f.wall']);

            if ($joinWallTagTables === true) {
                $query
                    ->join(['wt' => 'social_data_wall_tags'], 'wt.wall_id = f.wall', ['tagId' => 'wt.tag_id'])
                    ->join(['wtt' => 'social_data_tag'], 'wtt.id = wt.tag_id', ['tagName' => 'wtt.name']);
            }

            if ($joinFeedTagTables === true) {
                $query
                    ->join(['ft' => 'social_data_feed_tags'], 'ft.feed_id = f.id', ['tagId' => 'ft.tag_id'])
                    ->join(['ftt' => 'social_data_tag'], 'ftt.id = ft.tag_id', ['tagName' => 'ftt.name']);
            }
        });

        return $listing;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOutdatedSocialPosts(int $expireDays, bool $deletePoster = false)
    {
        $expireDate = Carbon::now()->subDays($expireDays);

        $listing = $this->getList();
        $listing->setUnpublished(true);
        $listing->addConditionParam('o_creationDate < ?', $expireDate->getTimestamp());

        /** @var Concrete $socialPost */
        foreach ($listing->getObjects() as $socialPost) {

            try {

                if ($deletePoster === true && $socialPost instanceof SocialPostInterface && $socialPost->getPoster() instanceof Asset) {
                    $socialPost->getPoster()->delete();
                }

                $socialPost->delete();

            } catch (\Exception $e) {
                // fail silently
            }

        }
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
