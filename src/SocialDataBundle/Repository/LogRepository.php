<?php

namespace SocialDataBundle\Repository;

use Carbon\Carbon;
use Doctrine\DBAL\Types\Type;
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

    public function findForConnectorEngine(int $connectorEngineId): iterable
    {
        $qb = $this->repository->createQueryBuilder('l');

        $query = $qb->where('l.connectorEngine = :connectorEngine')
            ->setParameter('connectorEngine', $connectorEngineId)
            ->addOrderBy('l.creationDate', 'DESC');

        return new Paginator($query);
    }

    public function findForWall(int $wallId): iterable
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
            ->setParameter('expires', $expireDate->toDateTime(), Type::DATETIME)
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
