<?php

namespace SocialDataBundle\Manager;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Pimcore\Model\DataObject\Concrete;
use SocialDataBundle\Model\FeedInterface;
use Doctrine\ORM\EntityManagerInterface;

class FeedPostManager implements FeedPostManagerInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function connectFeedWithPost(FeedInterface $feed, Concrete $socialPost)
    {
        if ($this->relationExists($feed, $socialPost) === true) {
            return;
        }

        $this->getConnection()->insert('social_data_feed_post', [
            'feed_id' => $feed->getId(),
            'post_id' => $socialPost->getId()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function removePostFromFeeds(Concrete $socialPost)
    {
        $this->getConnection()->delete('social_data_feed_post', [
            'post_id' => $socialPost->getId()
        ]);
    }

    /**
     * @param FeedInterface $feed
     * @param Concrete      $socialPost
     *
     * @return bool
     */
    protected function relationExists(FeedInterface $feed, Concrete $socialPost)
    {
        $qb = $this->getQueryBuilder()->select(['post_id', 'feed_id'])
            ->from('social_data_feed_post', 'fp')
            ->where('feed_id = :feedId')
            ->andWhere('post_id = :postId')
            ->setParameters([
                'feedId' => $feed->getId(),
                'postId' => $socialPost->getId()
            ]);

        $stmt = $qb->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * @return QueryBuilder
     */
    protected function getQueryBuilder()
    {
        return $this->getConnection()->createQueryBuilder();
    }

    /**
     * @return Connection
     */
    protected function getConnection()
    {
        return $this->entityManager->getConnection();
    }
}
