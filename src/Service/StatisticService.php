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
