<?php

namespace SocialDataBundle\EventListener\Maintenance;

use Pimcore\Maintenance\TaskInterface;
use SocialDataBundle\Repository\SocialPostRepositoryInterface;
use SocialDataBundle\Service\LockServiceInterface;

class CleanUpOldSocialPostsTask implements TaskInterface
{
    const LOCK_ID = 'social_data_maintenance_task_cleanup_old_social_posts';

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var bool
     */
    protected $deletePoster;

    /**
     * @var int
     */
    protected $expirationDays;

    /**
     * @var LockServiceInterface
     */
    protected $lockService;

    /**
     * @var SocialPostRepositoryInterface
     */
    protected $socialPostRepository;

    /**
     * @param bool                          $enabled
     * @param bool                          $deletePoster
     * @param int                           $expirationDays
     * @param LockServiceInterface          $lockService
     * @param SocialPostRepositoryInterface $socialPostRepository
     */
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

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->enabled === false) {
             $this->lockService->unLock(self::LOCK_ID);
            return;
        }

        // only run every 6 hours
        $seconds = intval(6 * 3600);

        if ($this->lockService->isLocked(self::LOCK_ID, $seconds)) {
            return;
        }

        $this->lockService->lock(self::LOCK_ID);
        $this->socialPostRepository->deleteOutdatedSocialPosts($this->expirationDays, $this->deletePoster);
    }
}
