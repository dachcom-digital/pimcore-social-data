<?php

namespace SocialDataBundle\EventListener\Maintenance;

use Pimcore\Maintenance\TaskInterface;
use SocialDataBundle\Repository\SocialPostRepositoryInterface;
use SocialDataBundle\Service\LockServiceInterface;

class CleanUpOldSocialPostsTask implements TaskInterface
{
    public const LOCK_ID = 'social_data_maintenance_task_cleanup_old_social_posts';

    protected bool $enabled;
    protected bool $deletePoster;
    protected int $expirationDays;
    protected LockServiceInterface $lockService;
    protected SocialPostRepositoryInterface $socialPostRepository;

    public function __construct(
        bool $enabled,
        bool $deletePoster,
        int $expirationDays,
        LockServiceInterface $lockService,
        SocialPostRepositoryInterface $socialPostRepository
    ) {
        $this->enabled = $enabled;
        $this->deletePoster = $deletePoster;
        $this->expirationDays = $expirationDays;
        $this->lockService = $lockService;
        $this->socialPostRepository = $socialPostRepository;
    }

    public function execute(): void
    {
        if ($this->enabled === false) {
             $this->lockService->unLock(self::LOCK_ID);
            return;
        }

        // only run every 6 hours
        $seconds = (int) (6 * 3600);

        if ($this->lockService->isLocked(self::LOCK_ID)) {
            return;
        }

        $this->lockService->lock(self::LOCK_ID, $seconds);
        $this->socialPostRepository->deleteOutdatedSocialPosts($this->expirationDays, $this->deletePoster);
    }
}
