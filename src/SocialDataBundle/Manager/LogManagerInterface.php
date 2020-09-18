<?php

namespace SocialDataBundle\Manager;

use Doctrine\ORM\Tools\Pagination\Paginator;
use SocialDataBundle\Model\LogEntryInterface;

interface LogManagerInterface
{
    /**
     * @param int $objectId
     *
     * @return Paginator|LogEntryInterface[]
     */
    public function getForObject(int $objectId);

    /**
     * @param int $connectorEngineId
     * @param int $offset
     * @param int $limit
     *
     * @return Paginator|LogEntryInterface[]
     */
    public function getForConnectorEngine(int $connectorEngineId, int $offset, int $limit);

    /**
     * @param int $connectorEngineId
     * @param int $objectId
     * @param int $offset
     * @param int $limit
     *
     * @return Paginator|LogEntryInterface[]
     */
    public function getForConnectorEngineAndObject(int $connectorEngineId, int $objectId, int $offset, int $limit);

    /**
     * @param int $connectorEngineId
     * @param int $objectId
     */
    public function deleteForConnectorEngineAndObject(int $connectorEngineId, int $objectId);

    /**
     * @param int $objectId
     */
    public function deleteForObject(int $objectId);

    /**
     * @throws \Exception
     */
    public function flushLogs();

    /**
     * @return LogEntryInterface
     */
    public function createNew();

    /**
     * @param string $connectorName
     *
     * @return LogEntryInterface
     */
    public function createNewForConnector(string $connectorName);

    /**
     * @param LogEntryInterface $logEntry
     *
     * @return LogEntryInterface
     */
    public function update(LogEntryInterface $logEntry);

    /**
     * @param LogEntryInterface $logEntry
     */
    public function delete(LogEntryInterface $logEntry);
}