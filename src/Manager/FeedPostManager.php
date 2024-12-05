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

namespace SocialDataBundle\Manager;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Pimcore\Model\DataObject\Concrete;
use SocialDataBundle\Model\FeedInterface;

class FeedPostManager implements FeedPostManagerInterface
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
    }

    public function connectFeedWithPost(FeedInterface $feed, Concrete $socialPost): void
    {
        if ($this->relationExists($feed, $socialPost) === true) {
            return;
        }

        $this->getConnection()->insert('social_data_feed_post', [
            'feed_id' => $feed->getId(),
            'post_id' => $socialPost->getId()
        ]);
    }

    public function removePostFromFeeds(Concrete $socialPost): void
    {
        $this->getConnection()->delete('social_data_feed_post', [
            'post_id' => $socialPost->getId()
        ]);
    }

    protected function relationExists(FeedInterface $feed, Concrete $socialPost): bool
    {
        $qb = $this->getQueryBuilder()->select(['post_id', 'feed_id'])
            ->from('social_data_feed_post', 'fp')
            ->where('feed_id = :feedId')
            ->andWhere('post_id = :postId')
            ->setParameters([
                'feedId' => $feed->getId(),
                'postId' => $socialPost->getId()
            ]);

        $stmt = $qb->executeQuery();

        return $stmt->rowCount() > 0;
    }

    protected function getQueryBuilder(): QueryBuilder
    {
        return $this->getConnection()->createQueryBuilder();
    }

    protected function getConnection(): Connection
    {
        return $this->entityManager->getConnection();
    }
}
