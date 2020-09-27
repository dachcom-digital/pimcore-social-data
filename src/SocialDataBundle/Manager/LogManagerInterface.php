<?php

namespace SocialDataBundle\Manager;

use Doctrine\ORM\Tools\Pagination\Paginator;
use SocialDataBundle\Model\LogEntryInterface;

interface LogManagerInterface
{
    /**
     * @param int $connectorEngineId
     *
     * @return Paginator|LogEntryInterface[]
     */
    public function getForConnectorEngine(int $connectorEngineId);

    /**
     * @param int $wallId
     *
     * @return Paginator|LogEntryInterface[]
     */
    public function getForWall(int $wallId);

    /**
     * @throws \Exception
     */
    public function flushLogs();

    /**
     * @return LogEntryInterface
     */
    public function createNew();

    /**
     * @param array $context
     *
     * @return LogEntryInterface
     */
    public function createNewForConnector(array $context);

    /**
     * @param LogEntryInterface $logEntry
     */
    public function update(LogEntryInterface $logEntry);

    /**
     * @param LogEntryInterface $logEntry
     */
    public function delete(LogEntryInterface $logEntry);
}