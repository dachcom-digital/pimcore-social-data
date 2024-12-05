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
