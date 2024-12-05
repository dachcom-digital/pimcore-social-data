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

use Pimcore\Model\Tool\TmpStore;

class LockService implements LockServiceInterface
{
    public function isLocked(string $token): bool
    {
        return $this->getLockToken($token) instanceof TmpStore;
    }

    public function lock(string $token, $lifeTime = 1800): void
    {
        if ($this->isLocked($token)) {
            return;
        }

        TmpStore::add($this->getNamespacedToken($token), 'Social Data', null, $lifeTime);
    }

    public function unLock(string $token): void
    {
        TmpStore::delete($this->getNamespacedToken($token));
    }

    protected function getLockToken(string $token): ?TmpStore
    {
        return TmpStore::get($this->getNamespacedToken($token));
    }

    protected function getNamespacedToken(string $token, string $namespace = 'social_data'): string
    {
        return sprintf('%s_%s', $namespace, $token);
    }
}
