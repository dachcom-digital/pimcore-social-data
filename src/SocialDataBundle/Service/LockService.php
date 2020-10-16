<?php

namespace SocialDataBundle\Service;

use Pimcore\Model\Tool\Lock;

class LockService implements LockServiceInterface
{
    /**
     * {@inheritDoc}
     */
    public function isLocked(string $key, int $expire = 1800)
    {
        return Lock::isLocked($key, $expire);
    }

    /**
     * {@inheritDoc}
     */
    public function lock(string $key)
    {
        Lock::lock($key);
    }

    /**
     * {@inheritDoc}
     */
    public function unLock(string $key)
    {
        return Lock::release($key);
    }
}

