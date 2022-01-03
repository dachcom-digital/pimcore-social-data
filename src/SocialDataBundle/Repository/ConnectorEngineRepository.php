<?php

namespace SocialDataBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use SocialDataBundle\Model\ConnectorEngine;
use SocialDataBundle\Model\ConnectorEngineInterface;

class ConnectorEngineRepository implements ConnectorEngineRepositoryInterface
{
    protected EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(ConnectorEngine::class);
    }

    public function findById($id): ?ConnectorEngineInterface
    {
        if ($id < 1) {
            return null;
        }

        return $this->repository->find($id);
    }

    public function findByName(string $name): ?ConnectorEngineInterface
    {
        if (empty($name)) {
            return null;
        }

        return $this->repository->findOneBy(['name' => $name]);
    }

    public function findIdByName(string $name): int
    {
        $form = $this->findByName($name);

        return $form->getId();
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }
}
