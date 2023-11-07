<?php

namespace SocialDataBundle\EventListener\Maintenance;

use SocialDataBundle\Repository\LogRepositoryInterface;
use Pimcore\Maintenance\TaskInterface;

class CleanUpLogsTask implements TaskInterface
{
    public function __construct(
        protected bool $enabled,
        protected int $expirationDays,
        protected LogRepositoryInterface $logRepository
    ) {
    }

    public function execute(): void
    {
        if ($this->enabled === false) {
            return;
        }

        $this->logRepository->deleteExpired($this->expirationDays);
    }
}
