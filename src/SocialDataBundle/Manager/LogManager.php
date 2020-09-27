<?php

namespace SocialDataBundle\Manager;

use SocialDataBundle\Connector\ConnectorEngineConfigurationInterface;
use SocialDataBundle\Model\FeedInterface;
use SocialDataBundle\Model\LogEntry;
use SocialDataBundle\Model\LogEntryInterface;
use SocialDataBundle\Model\WallInterface;
use SocialDataBundle\Repository\LogRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class LogManager implements LogManagerInterface
{
    /**
     * @var LogRepositoryInterface
     */
    protected $logRepository;

    /**
     * @var ConnectorManagerInterface
     */
    protected $connectorManager;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @param LogRepositoryInterface    $logRepository
     * @param ConnectorManagerInterface $connectorManager
     * @param EntityManagerInterface    $entityManager
     */
    public function __construct(
        LogRepositoryInterface $logRepository,
        ConnectorManagerInterface $connectorManager,
        EntityManagerInterface $entityManager
    ) {
        $this->logRepository = $logRepository;
        $this->connectorManager = $connectorManager;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getForConnectorEngine(int $connectorEngineId)
    {
        return $this->logRepository->findForConnectorEngine($connectorEngineId);
    }

    /**
     * {@inheritdoc}
     */
    public function getForWall(int $wallId)
    {
        return $this->logRepository->findForWall($wallId);
    }

    /**
     * {@inheritdoc}
     */
    public function flushLogs()
    {
        return $this->logRepository->truncateLogTable();
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        $logEntry = new LogEntry();
        $logEntry->setCreationDate(new \DateTime());

        return $logEntry;
    }

    /**
     * {@inheritdoc}
     */
    public function createNewForConnector(array $context)
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
            } elseif ($contextRow instanceof ConnectorEngineConfigurationInterface) {
                $logEntry->setConnectorEngine($contextRow);
            }
        }

        return $logEntry;
    }

    /**
     * {@inheritdoc}
     */
    public function update(LogEntryInterface $logEntry)
    {
        $this->entityManager->persist($logEntry);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(LogEntryInterface $logEntry)
    {
        $this->entityManager->remove($logEntry);
        $this->entityManager->flush();
    }
}
