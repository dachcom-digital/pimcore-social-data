<?php

namespace SocialDataBundle\Manager;

use SocialDataBundle\Model\WallInterface;

interface WallManagerInterface
{
    /**
     * @return array|WallInterface[]
     */
    public function getAll(): array;

    /**
     * @param string $name
     *
     * @return WallInterface|null
     */
    public function getByName(string $name);

    /**
     * @param int $id
     *
     * @return WallInterface|null
     */
    public function getById(int $id);

    /**
     * @param string $wallName
     * @param bool   $persist
     *
     * @return WallInterface
     */
    public function createNew(string $wallName, bool $persist = true);

    /**
     * @param WallInterface $wall
     *
     * @return WallInterface
     */
    public function update(WallInterface $wall);

    /**
     * @param WallInterface $wall
     */
    public function delete(WallInterface $wall);
}