<?php

namespace SocialDataBundle\Repository;

use SocialDataBundle\Model\LogEntryInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface LogRepositoryInterface
{
    /**
     * @param int $objectId
     *
     * @return Paginator|LogEntryInterface[]
     */
    public function findForObject(int $objectId);

    /**
     * @param int $connectorEngineId
     *
     * @return Paginator|LogEntryInterface[]
     */
    public function findForConnectorEngine(int $connectorEngineId);

    /**
     * @param int $connectorEngineId
     * @param int $objectId
     *
     * @return Paginator|LogEntryInterface[]
     */
    public function findForConnectorEngineAndObject(int $connectorEngineId, int $objectId);

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
     * @param int $expireDays
     */
    public function deleteExpired(int $expireDays);

    /**
     * @throws \Exception
     */
    public function truncateLogTable();
}
