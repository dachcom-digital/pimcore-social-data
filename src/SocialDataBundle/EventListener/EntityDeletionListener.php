<?php

namespace SocialDataBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Pimcore\Bundle\AdminBundle\Security\User\TokenStorageUserResolver;
use SocialDataBundle\Model\FeedInterface;
use SocialDataBundle\Model\LogEntry;
use SocialDataBundle\Model\WallInterface;

class EntityDeletionListener implements EventSubscriber
{
    protected TokenStorageUserResolver $userResolver;

    public function __construct(TokenStorageUserResolver $userResolver)
    {
        $this->userResolver = $userResolver;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::onFlush
        ];
    }

    public function onFlush(OnFlushEventArgs $event): void
    {
        $em = $event->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if ($entity instanceof FeedInterface || $entity instanceof WallInterface) {
                $this->logDeletion($entity, $em);
            }
        }
    }

    protected function logDeletion($object, EntityManager $entityManager): void
    {
        $logEntry = new LogEntry();
        $logEntry->setCreationDate(new \DateTime());
        $logEntry->setType('INFO');

        $user = $this->userResolver->getUser();
        $userId = $user ? $user->getId() : 'NULL';
        $userName = $user ? $user->getName() : 'Unknown';
        $message = null;

        if ($object instanceof FeedInterface) {
            $message = sprintf('User %s (%d) removed a feed from wall "%s"', $userName, $userId, $object->getWall()->getName());
            $logEntry->setWall($object->getWall());
            $logEntry->setConnectorEngine($object->getConnectorEngine());
        } elseif ($object instanceof WallInterface) {
            $message = sprintf('User %s (%d) removed wall "%s"', $userName, $userId, $object->getName());
        }

        $logEntry->setMessage($message);

        $entityManager->persist($logEntry);
        $logMetadata = $entityManager->getClassMetadata(LogEntry::class);
        $entityManager->getUnitOfWork()->computeChangeSet($logMetadata, $logEntry);
    }
}
