<?php

namespace SocialDataBundle\Manager;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Pimcore\Model\DataObject\Concrete;
use SocialDataBundle\Model\FeedInterface;
use Doctrine\ORM\EntityManagerInterface;

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
