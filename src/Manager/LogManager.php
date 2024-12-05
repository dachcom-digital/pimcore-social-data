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

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use SocialDataBundle\Model\ConnectorEngineInterface;
use SocialDataBundle\Model\FeedInterface;
use SocialDataBundle\Model\LogEntry;
use SocialDataBundle\Model\LogEntryInterface;
use SocialDataBundle\Model\WallInterface;
use SocialDataBundle\Repository\LogRepositoryInterface;

class LogManager implements LogManagerInterface
{
    public function __construct(
        protected LogRepositoryInterface $logRepository,
        protected ConnectorManagerInterface $connectorManager,
        protected EntityManagerInterface $entityManager
    ) {
    }

    public function getForConnectorEngine(int $connectorEngineId): Paginator
    {
        return $this->logRepository->findForConnectorEngine($connectorEngineId);
    }

    public function getForWall(int $wallId): Paginator
    {
        return $this->logRepository->findForWall($wallId);
    }

    public function flushLogs(): void
    {
        $this->logRepository->truncateLogTable();
    }

    public function createNew(): LogEntryInterface
    {
        $logEntry = new LogEntry();
        $logEntry->setCreationDate(new \DateTime());

        return $logEntry;
    }

    public function createNewForConnector(array $context): LogEntryInterface
    {
        $logEntry = new LogEntry();
        $logEntry->setCreationDate(new \DateTime());

        foreach ($context as $contextRow) {
            if ($contextRow instanceof FeedInterface) {
                $logEntry->setFeed($contextRow);
                $logEntry->setWall($contextRow->getWall());
                $logEntry->setConnectorEngine($contextRow->getConnectorEngine());
            } elseif ($contextRow instanceof WallInterface) {
                $logEntry->setWall($contextRow);
            } elseif ($contextRow instanceof ConnectorEngineInterface) {
                $logEntry->setConnectorEngine($contextRow);
            }
        }

        return $logEntry;
    }

    public function update(LogEntryInterface $logEntry): void
    {
        $this->entityManager->persist($logEntry);
        $this->entityManager->flush();
    }

    public function delete(LogEntryInterface $logEntry): void
    {
        $this->entityManager->remove($logEntry);
        $this->entityManager->flush();
    }
}
