<?php

namespace SocialDataBundle\Repository;

use SocialDataBundle\Model\LogEntryInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface LogRepositoryInterface
{
    public function findForConnectorEngine(int $connectorEngineId): Paginator;

    public function findForWall(int $wallId): Paginator;

    public function deleteExpired(int $expireDays): void;

    /**
     * @throws \Exception
     */
    public function truncateLogTable(): void;
}
