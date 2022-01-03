<?php

namespace SocialDataBundle\EventListener\Maintenance;

use SocialDataBundle\Repository\LogRepositoryInterface;
use Pimcore\Maintenance\TaskInterface;

class CleanUpLogsTask implements TaskInterface
{
    protected bool $enabled;
    protected int $expirationDays;
    protected LogRepositoryInterface $logRepository;

    public function __construct(
        bool $enabled,
        int $expirationDays,
        LogRepositoryInterface $logRepository
    ) {
        $this->enabled = $enabled;
        $this->expirationDays = $expirationDays;
        $this->logRepository = $logRepository;
    }

    public function execute(): void
    {
        if ($this->enabled === false) {
            return;
        }

        $this->logRepository->deleteExpired($this->expirationDays);
    }
}
