<?php

namespace SocialDataBundle\Service;

use Pimcore\Model\DataObject\Concrete;
use SocialDataBundle\Model\WallInterface;
use SocialDataBundle\Repository\SocialPostRepositoryInterface;

class StatisticService implements StatisticServiceInterface
{
    public function __construct(protected SocialPostRepositoryInterface $socialPostRepository)
    {
    }

    public function getWallStatistics(WallInterface $wall): array
    {
        $unpublishedCounter = 0;
        $publishedCounter = 0;
        $posts = $this->socialPostRepository->findByWallId($wall->getId(), true);

        /** @var Concrete $post */
        foreach ($posts as $post) {
            if ($post->isPublished() === true) {
                $publishedCounter++;
            } else {
                $unpublishedCounter++;
            }
        }

        return [
            'available_wall_posts'   => count($posts),
            'published_wall_posts'   => $publishedCounter,
            'unpublished_wall_posts' => $unpublishedCounter,
            'orphaned_wall_posts'    => 'Not Implemented'
        ];
    }
}
