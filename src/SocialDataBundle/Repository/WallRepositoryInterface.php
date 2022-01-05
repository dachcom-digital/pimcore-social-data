<?php

namespace SocialDataBundle\Repository;

use SocialDataBundle\Model\WallInterface;

interface WallRepositoryInterface
{
    /**
     * @return array<int, WallInterface>
     */
    public function findAll(): array;

    public function findById(int $id): ?WallInterface;

    public function findByName(string $name): ?WallInterface;
}
