<?php

namespace SocialDataBundle\EventListener\Maintenance;

use SocialDataBundle\Processor\SocialPostBuilderProcessor;
use Pimcore\Maintenance\TaskInterface;
use SocialDataBundle\Service\LockServiceInterface;

class FetchSocialPostsTask implements TaskInterface
{
    const LOCK_ID = 'social_data_maintenance_task_fetch_social_posts';

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var float
     */
    protected $interval;

    /**
     * @var LockServiceInterface
     */
    protected $lockService;

    /**
     * @var SocialPostBuilderProcessor
     */
    protected $socialPostBuilderProcessor;

    /**
     * @param bool                       $enabled
     * @param float                      $interval
     * @param LockServiceInterface       $lockService
     * @param SocialPostBuilderProcessor $socialPostBuilderProcessor
     */
    public function __construct(
        bool $enabled,
        float $interval,
        LockServiceInterface       $lockService,
        SocialPostBuilderProcessor $socialPostBuilderProcessor
    ) {
        $this->enabled = $enabled;
        $this->interval = $interval;
        $this->lockService = $lockService;
        $this->socialPostBuilderProcessor = $socialPostBuilderProcessor;
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

        $seconds = intval($this->interval * 3600);

        if ($this->lockService->isLocked(self::LOCK_ID, $seconds)) {
            return;
        }

        $this->lockService->lock(self::LOCK_ID);
        $this->socialPostBuilderProcessor->process(false, null);
    }
}
