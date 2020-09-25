<?php

namespace SocialDataBundle\Manager;

use SocialDataBundle\Model\Wall;
use Doctrine\ORM\EntityManagerInterface;
use SocialDataBundle\Model\WallInterface;
use SocialDataBundle\Repository\WallRepositoryInterface;

class WallManager implements WallManagerInterface
{
    /**
     * @var WallRepositoryInterface
     */
    protected $wallRepository;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @param WallRepositoryInterface $wallRepository
     * @param EntityManagerInterface  $entityManager
     */
    public function __construct(
        WallRepositoryInterface $wallRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->wallRepository = $wallRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(): array
    {
        return $this->wallRepository->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function getByName(string $name)
    {
        return $this->wallRepository->findByName($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getById(int $id)
    {
        return $this->wallRepository->findById($id);
    }

    /**
     * {@inheritdoc}
     */
    public function createNew(string $wallName, bool $persist = true)
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

    /**
     * {@inheritdoc}
     */
    public function update(WallInterface $wall)
    {
        $this->entityManager->persist($wall);
        $this->entityManager->flush();

        return $wall;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(WallInterface $wall)
    {
        $this->entityManager->remove($wall);
        $this->entityManager->flush();
    }
}
