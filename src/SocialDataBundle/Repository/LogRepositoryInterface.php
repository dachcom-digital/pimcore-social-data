<?php

namespace SocialDataBundle\Repository;

use SocialDataBundle\Model\LogEntryInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface LogRepositoryInterface
{
    public function findForConnectorEngine(int $connectorEngineId): iterable;

    public function findForWall(int $wallId): iterable;

    public function deleteExpired(int $expireDays): void;

    /**
     * @throws \Exception
     */
    public function truncateLogTable(): void;
}
