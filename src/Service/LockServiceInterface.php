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

namespace SocialDataBundle\Service;

interface LockServiceInterface
{
    public const SOCIAL_POST_BUILD_PROCESS_ID = 'social-data-post-build-process.pid';

    public function isLocked(string $token): bool;

    public function lock(string $token, $lifeTime = 1800): void;

    public function unLock(string $token): void;
}
