<?php

namespace SocialDataBundle\Manager;

use SocialDataBundle\Model\TagInterface;
use SocialDataBundle\Model\WallInterface;

interface WallManagerInterface
{
    /**
     * @return array<int, WallInterface>
     */
    public function getAll(): array;

    public function getByName(string $name): ?WallInterface;

    public function getById(int $id): ?WallInterface;

    public function createNew(string $wallName, bool $persist = true): WallInterface;

    /**
     * @return array<int, TagInterface>
     */
    public function getAvailableTags(string $type): array;

    public function update(WallInterface $wall): WallInterface;

    public function delete(WallInterface $wall): void;
}