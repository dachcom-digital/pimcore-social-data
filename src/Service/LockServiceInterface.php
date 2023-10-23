<?php

namespace SocialDataBundle\Service;

interface LockServiceInterface
{
    public const SOCIAL_POST_BUILD_PROCESS_ID = 'social-data-post-build-process.pid';

    public function isLocked(string $token): bool;

    public function lock(string $token, $lifeTime = 1800): void;

    public function unLock(string $token): void;
}
