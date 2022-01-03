<?php

namespace SocialDataBundle\Manager;

use Doctrine\ORM\Tools\Pagination\Paginator;
use SocialDataBundle\Model\ConnectorEngineInterface;
use SocialDataBundle\Model\FeedInterface;
use SocialDataBundle\Model\LogEntry;
use SocialDataBundle\Model\LogEntryInterface;
use SocialDataBundle\Model\WallInterface;
use SocialDataBundle\Repository\LogRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class LogManager implements LogManagerInterface
{
    protected LogRepositoryInterface $logRepository;
    protected ConnectorManagerInterface $connectorManager;
    protected EntityManagerInterface $entityManager;

    public function __construct(
        LogRepositoryInterface $logRepository,
        ConnectorManagerInterface $connectorManager,
        EntityManagerInterface $entityManager
    ) {
        $this->logRepository = $logRepository;
        $this->connectorManager = $connectorManager;
        $this->entityManager = $entityManager;
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
