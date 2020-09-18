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
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(LogEntry::class);
    }

    /**
     * {@inheritdoc}
     */
    public function findForObject(int $objectId)
    {
        $qb = $this->repository->createQueryBuilder('l');

        $query = $qb->where('l.objectId = :objectId')
            ->setParameter('objectId', $objectId);

        return new Paginator($query);
    }

    /**
     * {@inheritdoc}
     */
    public function findForConnectorEngine(int $connectorEngineId)
    {
        $qb = $this->repository->createQueryBuilder('l');

        $query = $qb->where('l.connectorEngine = :connectorEngine')
            ->setParameter('connectorEngine', $connectorEngineId);

        return new Paginator($query);
    }

    /**
     * {@inheritdoc}
     */
    public function findForConnectorEngineAndObject(int $connectorEngineId, int $objectId)
    {
        $qb = $this->repository->createQueryBuilder('l');

        $query = $qb->where('l.objectId = :objectId')
            ->andWhere('l.connectorEngine = :connectorEngine')
            ->addOrderBy('l.creationDate', 'DESC')
            ->addOrderBy('l.id', 'DESC')
            ->setParameter('objectId', $objectId)
            ->setParameter('connectorEngine', $connectorEngineId);

        return new Paginator($query);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteForConnectorEngineAndObject(int $connectorEngineId, int $objectId)
    {
        $qb = $this->repository->createQueryBuilder('l');

        $query = $qb->delete()
            ->where('l.objectId = :objectId')
            ->andWhere('l.connectorEngine = :connectorEngine')
            ->setParameter('objectId', $objectId)
            ->setParameter('connectorEngine', $connectorEngineId)
            ->getQuery();

        $query->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteForObject(int $objectId)
    {
        $qb = $this->repository->createQueryBuilder('l');

        $query = $qb->delete()
            ->where('l.objectId = :objectId')
            ->setParameter('objectId', $objectId)
            ->getQuery();

        $query->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteExpired(int $expireDays)
    {
        $qb = $this->repository->createQueryBuilder('l');
        $expireDate = Carbon::now()->subDays($expireDays);

        $query = $qb->delete()
            ->where('l.creationDate < :expires')
            ->setParameter('expires', $expireDate->toDateTime(), Type::DATETIME)
            ->getQuery();

        $query->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function truncateLogTable()
    {
        $connection = $this->entityManager->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->executeUpdate($platform->getTruncateTableSQL('social_data_log', true));
    }
}
