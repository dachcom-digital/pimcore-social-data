<?php

namespace SocialDataBundle\Service;

interface LockServiceInterface
{
    const SOCIAL_POST_BUILD_PROCESS_ID = 'social-data-post-build-process.pid';

    /**
     * @param string $key
     *
     * @return bool
     */
    public function isLocked(string $key);

    /**
     * @param string $key
     */
    public function lock(string $key);

    /**
     * @param string $key
     */
    public function unLock(string $key);
}
