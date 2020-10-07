<?php

namespace SocialDataBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\ORMException;
use Pimcore\Bundle\AdminBundle\Security\User\TokenStorageUserResolver;
use SocialDataBundle\Model\FeedInterface;
use SocialDataBundle\Model\LogEntry;
use SocialDataBundle\Model\WallInterface;

class EntityDeletionListener implements EventSubscriber
{
    /**
     * @var TokenStorageUserResolver
     */
    protected $userResolver;

    /**
     * @param TokenStorageUserResolver $userResolver
     */
    public function __construct(TokenStorageUserResolver $userResolver)
    {
        $this->userResolver = $userResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::onFlush
        ];
    }

    /**
     * @param OnFlushEventArgs $event
     *
     * @throws ORMException
     */
    public function onFlush(OnFlushEventArgs $event)
    {
        $em = $event->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if ($entity instanceof FeedInterface || $entity instanceof WallInterface) {
                $this->logDeletion($entity, $em);
            }
        }
    }

    /**
     * @param mixed         $object
     * @param EntityManager $entityManager
     *
     * @throws ORMException
     */
    protected function logDeletion($object, EntityManager $entityManager)
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
