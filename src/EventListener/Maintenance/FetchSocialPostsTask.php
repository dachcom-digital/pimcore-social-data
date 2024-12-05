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

namespace SocialDataBundle\EventListener\Maintenance;

use Pimcore\Maintenance\TaskInterface;
use SocialDataBundle\Processor\SocialPostBuilderProcessor;
use SocialDataBundle\Service\LockServiceInterface;

class FetchSocialPostsTask implements TaskInterface
{
    public const LOCK_ID = 'social_data_maintenance_task_fetch_social_posts';

    public function __construct(
        protected bool $enabled,
        protected float $interval,
        protected LockServiceInterface $lockService,
        protected SocialPostBuilderProcessor $socialPostBuilderProcessor
    ) {
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
