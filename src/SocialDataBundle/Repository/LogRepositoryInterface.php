<?php

namespace SocialDataBundle\Repository;

use SocialDataBundle\Model\LogEntryInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface LogRepositoryInterface
{
    /**
     * @param int $connectorEngineId
     *
     * @return Paginator|LogEntryInterface[]
     */
    public function findForConnectorEngine(int $connectorEngineId);

    /**
     * @param int $expireDays
     */
    public function deleteExpired(int $expireDays);

    /**
     * @throws \Exception
     */
    public function truncateLogTable();
}
