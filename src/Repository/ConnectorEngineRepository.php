<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

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
