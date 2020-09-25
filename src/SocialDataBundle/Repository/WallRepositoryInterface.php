<?php

namespace SocialDataBundle\Repository;

use SocialDataBundle\Model\WallInterface;

interface WallRepositoryInterface
{
    /**
     * @return array
     */
    public function findAll(): array;

    /**
     * @param int $id
     *
     * @return WallInterface|null
     */
    public function findById(int $id): ?WallInterface;

    /**
     * @param string $name
     *
     * @return WallInterface|null
     */
    public function findByName(string $name): ?WallInterface;
}
