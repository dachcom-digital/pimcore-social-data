<?php

namespace SocialDataBundle\Service;

interface LockServiceInterface
{
    const SOCIAL_POST_BUILD_PROCESS_ID = 'social-data-post-build-process.pid';

    /**
     * @param string $key
     * @param int    $expire
     *
     * @return bool
     */
    public function isLocked(string $key, int $expire = 1800);

    /**
     * @param string $key
     */
    public function lock(string $key);

    /**
     * @param string $key
     */
    public function unLock(string $key);
}
