<?php

namespace SocialDataBundle\Manager;

use Doctrine\ORM\Tools\Pagination\Paginator;
use SocialDataBundle\Model\LogEntryInterface;

interface LogManagerInterface
{
    public function getForConnectorEngine(int $connectorEngineId): Paginator;

    public function getForWall(int $wallId): Paginator;

    /**
     * @throws \Exception
     */
    public function flushLogs(): void;

    public function createNew(): LogEntryInterface;

    public function createNewForConnector(array $context): LogEntryInterface;

    public function update(LogEntryInterface $logEntry): void;

    public function delete(LogEntryInterface $logEntry): void;
}