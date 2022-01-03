<?php

namespace SocialDataBundle\Repository;

use Carbon\Carbon;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Listing;
use SocialDataBundle\Model\SocialPostInterface;
use SocialDataBundle\Service\EnvironmentService;
use Doctrine\DBAL\Query\QueryBuilder;

class SocialPostRepository implements SocialPostRepositoryInterface
{
    protected EnvironmentService $environmentService;

    public function __construct(EnvironmentService $environmentService)
    {
        $this->environmentService = $environmentService;
    }

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

    public function findAll(string $socialPostType, bool $unpublished = false): array
    {
        return $this->findAllListing($socialPostType, $unpublished)->getObjects();
    }

    public function findAllListing(string $socialPostType, bool $unpublished = false): Listing
    {
        $listing = $this->getList();
        $listing->setUnpublished($unpublished);
        $listing->addConditionParam('socialType = ?', (string) $socialPostType);

        return $listing;
    }

    public function findBySocialType(string $socialPostType, bool $unpublished = false): array
    {
        return $this->findBySocialTypeListing($socialPostType, $unpublished)->getObjects();
    }

    public function findBySocialTypeListing(string $socialPostType, bool $unpublished = false): Listing
    {
        $listing = $this->getList();
        $listing->setUnpublished($unpublished);
        $listing->addConditionParam('socialType = ?', (string) $socialPostType);

        return $listing;
    }

    public function findByWallId(int $wallId, bool $unpublished = false): array
    {
        return $this->findByWallIdListing($wallId, $unpublished)->getObjects();
    }

    public function findByWallIdListing(int $wallId, bool $unpublished = false): Listing
    {
        $listing = $this->getFeedPostJoinListing($unpublished);
        $listing->addConditionParam('f.wall = ?', (string) $wallId);

        return $listing;
    }

    public function findByFeedId(int $feedId, bool $unpublished = false): array
    {
        return $this->findByFeedIdListing($feedId, $unpublished)->getObjects();
    }

    public function findByFeedIdListing(int $feedId, bool $unpublished = false): Listing
    {
        $listing = $this->getFeedPostJoinListing($unpublished);
        $listing->addConditionParam('f.id = ?', (string) $feedId);

        return $listing;
    }

    public function findBySocialTypeAndWallId(string $socialPostType, int $wallId, bool $unpublished = false): array
    {
        return $this->findBySocialTypeAndWallIdListing($socialPostType, $wallId, $unpublished)->getObjects();
    }

    public function findBySocialTypeAndWallIdListing(string $socialPostType, int $wallId, bool $unpublished = false): Listing
    {
        $listing = $this->getFeedPostJoinListing($unpublished);
        $listing->addConditionParam('f.wall = ?', (string) $wallId);
        $listing->addConditionParam('socialType = ?', $socialPostType);

        return $listing;
    }

    public function findBySocialTypeAndFeedId(string $socialPostType, int $feedId, bool $unpublished = false): array
    {
        return $this->findBySocialTypeAndFeedIdListing($socialPostType, $feedId, $unpublished)->getObjects();
    }

    public function findBySocialTypeAndFeedIdListing(string $socialPostType, int $feedId, bool $unpublished = false): Listing
    {
        $listing = $this->getFeedPostJoinListing($unpublished);
        $listing->addConditionParam('f.id = ?', (string) $feedId);
        $listing->addConditionParam('socialType = ?', $socialPostType);

        return $listing;
    }

    public function findBySocialTypeAndWallIdAndFeedId(string $socialPostType, int $wallId, int $feedId, bool $unpublished = false): array
    {
        return $this->findBySocialTypeAndWallIdAndFeedIdListing($socialPostType, $wallId, $feedId, $unpublished)->getObjects();
    }

    public function findBySocialTypeAndWallIdAndFeedIdListing(string $socialPostType, int $wallId, int $feedId, bool $unpublished = false): Listing
    {
        $listing = $this->getFeedPostJoinListing($unpublished);
        $listing->addConditionParam('f.wall = ?', (string) $wallId);
        $listing->addConditionParam('f.id = ?', (string) $feedId);
        $listing->addConditionParam('socialType = ?', $socialPostType);

        return $listing;
    }

    public function findByTag(array $wallTags = [], array $feedTags = [], bool $unpublished = false): array
    {
        return $this->findByTagListing($wallTags, $feedTags, $unpublished)->getObjects();
    }

    public function findByTagListing(array $wallTags = [], array $feedTags = [], bool $unpublished = false): Listing
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

    public function findSocialTypeAndByTag(string $socialPostType, array $wallTags = [], array $feedTags = [], bool $unpublished = false): array
    {
        return $this->findSocialTypeAndByTagListing($socialPostType, $wallTags, $feedTags, $unpublished)->getObjects();
    }

    public function findSocialTypeAndByTagListing(string $socialPostType, array $wallTags = [], array $feedTags = [], bool $unpublished = false): Listing
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

    public function getList(): Listing
    {
        $listingClass = sprintf('\Pimcore\Model\DataObject\%s\Listing', ucfirst($this->environmentService->getSocialPostDataClass()));

        return new $listingClass();
    }

    public function getFeedPostJoinListing(bool $unpublished = false, bool $joinWallTagTables = false, bool $joinFeedTagTables = false): Listing
    {
        $listing = $this->getList();
        $listing->setUnpublished($unpublished);

        $aliasFrom = $listing->getDao()->getTableName();
        $listing->onCreateQueryBuilder(function (QueryBuilder $query) use ($joinWallTagTables, $joinFeedTagTables, $aliasFrom) {

            $query
                ->join($aliasFrom, 'social_data_feed_post', 'fp', 'fp.post_id = o_id')
                ->join($aliasFrom, 'social_data_feed', 'f', 'f.id = fp.feed_id');

            if ($joinWallTagTables === true) {
                $query
                    ->join($aliasFrom, 'social_data_wall_tags', 'wt', 'wt.wall_id = f.wall')
                    ->join($aliasFrom, 'social_data_tag', 'wtt', 'wtt.id = wt.tag_id');
            }

            if ($joinFeedTagTables === true) {
                $query
                    ->join($aliasFrom, 'social_data_feed_tags', 'ft', 'ft.feed_id = f.id')
                    ->join($aliasFrom, 'social_data_tag', 'ftt', 'ftt.id = ft.tag_id');
            }
        });

        return $listing;
    }

    public function deleteOutdatedSocialPosts(int $expireDays, bool $deletePoster = false): void
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

    public function getClassId(): string
    {
        /** @var Concrete $concreteObject */
        $concreteObject = sprintf('\Pimcore\Model\DataObject\%s', ucfirst($this->environmentService->getSocialPostDataClass()));

        return $concreteObject::classId();
    }
}
