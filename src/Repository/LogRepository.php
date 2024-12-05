<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

namespace SocialDataBundle\Repository;

use Carbon\Carbon;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use SocialDataBundle\Model\LogEntry;

class LogRepository implements LogRepositoryInterface
{
    protected EntityManagerInterface $entityManager;
    protected EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(LogEntry::class);
    }

    public function findForConnectorEngine(int $connectorEngineId): Paginator
    {
        $qb = $this->repository->createQueryBuilder('l');

        $query = $qb->where('l.connectorEngine = :connectorEngine')
            ->setParameter('connectorEngine', $connectorEngineId)
            ->addOrderBy('l.creationDate', 'DESC');

        return new Paginator($query);
    }

    public function findForWall(int $wallId): Paginator
    {
        $qb = $this->repository->createQueryBuilder('l');

        $query = $qb->where('l.wall = :wall')
            ->setParameter('wall', $wallId)
            ->addOrderBy('l.creationDate', 'DESC');

        return new Paginator($query);
    }

    public function deleteExpired(int $expireDays): void
    {
        $qb = $this->repository->createQueryBuilder('l');
        $expireDate = Carbon::now()->subDays($expireDays);

        $query = $qb->delete()
            ->where('l.creationDate < :expires')
            ->setParameter('expires', $expireDate->toDateTime(), Types::DATETIME_MUTABLE)
            ->getQuery();

        $query->execute();
    }

    public function truncateLogTable(): void
    {
        $connection = $this->entityManager->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->executeStatement($platform->getTruncateTableSQL('social_data_log', true));
    }
}
