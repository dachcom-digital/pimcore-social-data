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

use Pimcore\Model\DataObject\Concrete;
use SocialDataBundle\Model\FeedInterface;
use SocialDataBundle\Model\SocialPostInterface;
use SocialDataBundle\Model\WallInterface;

interface SocialPostManagerInterface
{
    /**
     * @throws \Exception
     */
    public function checkWallStoragePaths(WallInterface $wall): void;

    public function provideSocialPostEntity(string|int|null $filteredId, string $connectorName, FeedInterface $feed): ?SocialPostInterface;

    public function persistSocialPostEntity(Concrete $post, FeedInterface $feed, bool $forceProcessing): void;
}
