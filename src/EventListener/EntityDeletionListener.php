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

namespace SocialDataBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Pimcore\Security\User\TokenStorageUserResolver;
use SocialDataBundle\Model\FeedInterface;
use SocialDataBundle\Model\LogEntry;
use SocialDataBundle\Model\WallInterface;

class EntityDeletionListener implements EventSubscriber
{
    public function __construct(protected TokenStorageUserResolver $userResolver)
    {
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
