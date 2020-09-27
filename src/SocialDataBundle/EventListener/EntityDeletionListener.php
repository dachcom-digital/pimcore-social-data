<?php

namespace SocialDataBundle\EventListener;

use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Pimcore\Bundle\AdminBundle\Security\User\TokenStorageUserResolver;
use SocialDataBundle\Logger\LoggerInterface;
use SocialDataBundle\Model\FeedInterface;
use SocialDataBundle\Model\WallInterface;

class EntityDeletionListener implements EventSubscriber
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var TokenStorageUserResolver
     */
    protected $userResolver;

    /**
     * @param LoggerInterface          $logger
     * @param TokenStorageUserResolver $userResolver
     */
    public function __construct(LoggerInterface $logger, TokenStorageUserResolver $userResolver)
    {
        $this->logger = $logger;
        $this->userResolver = $userResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postRemove
        ];
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postRemove(LifecycleEventArgs $event)
    {
        $object = $event->getObject();
        $user = $this->userResolver->getUser();
        $userId = $user ? $user->getId() : 'NULL';
        $userName = $user ? $user->getName() : 'Unknown';

        if ($object instanceof FeedInterface) {
            $message = sprintf('User %s (%d) removed a feed from wall "%s"', $userName, $userId, $object->getWall()->getName());
            $this->logger->info($message, [$object->getWall()]);
        } elseif ($object instanceof WallInterface) {
            $message = sprintf('User %s (%d) removed wall "%s"', $userName, $userId, $object->getName());
            $this->logger->info($message);
        }
    }
}
