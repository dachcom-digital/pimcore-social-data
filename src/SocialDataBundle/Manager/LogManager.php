<?php

namespace SocialDataBundle\Manager;

use SocialDataBundle\Model\LogEntry;
use SocialDataBundle\Model\LogEntryInterface;
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
    public function getForObject(int $objectId)
    {
        return $this->logRepository->findForObject($objectId);
    }

    /**
     * {@inheritdoc}
     */
    public function getForConnectorEngine(int $connectorEngineId, int $offset, int $limit)
    {
        return $this->logRepository->findForConnectorEngine($connectorEngineId);
    }

    /**
     * {@inheritdoc}
     */
    public function getForConnectorEngineAndObject(int $connectorEngineId, int $objectId, int $offset, int $limit)
    {
        return $this->logRepository->findForConnectorEngineAndObject($connectorEngineId, $objectId);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteForConnectorEngineAndObject(int $connectorEngineId, int $objectId)
    {
        return $this->logRepository->deleteForConnectorEngineAndObject($connectorEngineId, $objectId);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteForObject(int $objectId)
    {
        return $this->logRepository->deleteForObject($objectId);
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
    public function createNewForConnector(string $connectorName)
    {
        $connectorEngine = $this->connectorManager->getEngineByName($connectorName);

        $logEntry = new LogEntry();
        $logEntry->setConnectorEngine($connectorEngine);
        $logEntry->setCreationDate(new \DateTime());

        return $logEntry;
    }

    /**
     * {@inheritdoc}
     */
    public function update(LogEntryInterface $logEntry)
    {
        $this->entityManager->persist($logEntry);
        $this->entityManager->flush();

        return $logEntry;
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
