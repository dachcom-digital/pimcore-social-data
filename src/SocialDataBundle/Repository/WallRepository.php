<?php

namespace SocialDataBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use SocialDataBundle\Model\Wall;
use SocialDataBundle\Model\WallInterface;

class WallRepository implements WallRepositoryInterface
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
        $this->repository = $entityManager->getRepository(Wall::class);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function findById(int $id): ?WallInterface
    {
        return $this->repository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findByName(string $name): ?WallInterface
    {
        return $this->repository->findOneBy(['name' => $name]);
    }
}
