<?php

namespace SocialDataBundle\EventListener\Maintenance;

use SocialDataBundle\Processor\SocialPostBuilderProcessor;
use Pimcore\Maintenance\TaskInterface;
use SocialDataBundle\Service\LockServiceInterface;

class FetchSocialPostsTask implements TaskInterface
{
    public const LOCK_ID = 'social_data_maintenance_task_fetch_social_posts';

    protected bool $enabled;
    protected float $interval;
    protected LockServiceInterface $lockService;
    protected SocialPostBuilderProcessor $socialPostBuilderProcessor;

    public function __construct(
        bool $enabled,
        float $interval,
        LockServiceInterface $lockService,
        SocialPostBuilderProcessor $socialPostBuilderProcessor
    ) {
        $this->enabled = $enabled;
        $this->interval = $interval;
        $this->lockService = $lockService;
        $this->socialPostBuilderProcessor = $socialPostBuilderProcessor;
    }

    public function execute(): void
    {
        if ($this->enabled === false) {
            $this->lockService->unLock(self::LOCK_ID);
            return;
        }

        $seconds = (int) ($this->interval * 3600);

        if ($this->lockService->isLocked(self::LOCK_ID)) {
            return;
        }

        $this->lockService->lock(self::LOCK_ID, $seconds);
        $this->socialPostBuilderProcessor->process(false, null);
    }
}
