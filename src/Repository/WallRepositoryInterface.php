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
