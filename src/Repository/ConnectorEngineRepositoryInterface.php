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
