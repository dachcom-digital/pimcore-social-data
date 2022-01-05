<?php

namespace SocialDataBundle\Repository;

use SocialDataBundle\Model\ConnectorEngineInterface;

interface ConnectorEngineRepositoryInterface
{
    public function findById($id): ?ConnectorEngineInterface;

    public function findByName(string $name): ?ConnectorEngineInterface;

    public function findIdByName(string $name): int;

    /**
     * @return array<int, ConnectorEngineInterface>
     */
    public function findAll(): array;
}
