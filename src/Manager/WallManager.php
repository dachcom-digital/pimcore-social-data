<?php

namespace SocialDataBundle\Manager;

use SocialDataBundle\Model\Tag;
use SocialDataBundle\Model\Wall;
use Doctrine\ORM\EntityManagerInterface;
use SocialDataBundle\Model\WallInterface;
use SocialDataBundle\Repository\WallRepositoryInterface;

class WallManager implements WallManagerInterface
{
    public function __construct(
        protected WallRepositoryInterface $wallRepository,
        protected EntityManagerInterface $entityManager
    ) {
    }

    public function getAll(): array
    {
        return $this->wallRepository->findAll();
    }

    public function getByName(string $name): ?WallInterface
    {
        return $this->wallRepository->findByName($name);
    }

    public function getById(int $id): ?WallInterface
    {
        return $this->wallRepository->findById($id);
    }

    public function createNew(string $wallName, bool $persist = true): WallInterface
    {
        $wall = new Wall();
        $wall->setName($wallName);
        $wall->setCreationDate(new \DateTime());

        if ($persist === false) {
            return $wall;
        }

        $this->entityManager->persist($wall);
        $this->entityManager->flush();

        return $wall;
    }

    public function getAvailableTags(string $type): array
    {
        return $this->entityManager->getRepository(Tag::class)->findBy([
            'type' => $type,
        ]);
    }

    public function update(WallInterface $wall): WallInterface
    {
        $this->entityManager->persist($wall);
        $this->entityManager->flush();

        return $wall;
    }

    public function delete(WallInterface $wall): void
    {
        $this->entityManager->remove($wall);
        $this->entityManager->flush();
    }
}
