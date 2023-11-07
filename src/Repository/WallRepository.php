<?php

namespace SocialDataBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use SocialDataBundle\Model\Wall;
use SocialDataBundle\Model\WallInterface;

class WallRepository implements WallRepositoryInterface
{
    protected EntityManagerInterface $entityManager;
    protected EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Wall::class);
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    public function findById(int $id): ?WallInterface
    {
        return $this->repository->find($id);
    }

    public function findByName(string $name): ?WallInterface
    {
        return $this->repository->findOneBy(['name' => $name]);
    }
}
