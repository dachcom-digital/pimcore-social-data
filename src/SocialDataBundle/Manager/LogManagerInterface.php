<?php

namespace SocialDataBundle\Manager;

use SocialDataBundle\Model\LogEntryInterface;

interface LogManagerInterface
{
    /**
     * @return array<int, LogEntryInterface>
     */
    public function getForConnectorEngine(int $connectorEngineId): iterable;

    /**
     * @return array<int, LogEntryInterface>
     */
    public function getForWall(int $wallId): iterable;

    /**
     * @throws \Exception
     */
    public function flushLogs(): void;

    public function createNew(): LogEntryInterface;

    public function createNewForConnector(array $context): LogEntryInterface;

    public function update(LogEntryInterface $logEntry): void;

    public function delete(LogEntryInterface $logEntry): void;
}