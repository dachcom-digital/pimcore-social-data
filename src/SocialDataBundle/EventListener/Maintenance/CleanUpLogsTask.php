<?php

namespace SocialDataBundle\EventListener\Maintenance;

use SocialDataBundle\Repository\LogRepositoryInterface;
use Pimcore\Maintenance\TaskInterface;

class CleanUpLogsTask implements TaskInterface
{
    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var int
     */
    protected $expirationDays;

    /**
     * @var LogRepositoryInterface
     */
    protected $logRepository;

    /**
     * @param bool                   $enabled
     * @param int                    $expirationDays
     * @param LogRepositoryInterface $logRepository
     */
    public function __construct(
        bool $enabled,
        int $expirationDays,
        LogRepositoryInterface $logRepository
    ) {
        $this->enabled = $enabled;
        $this->expirationDays = $expirationDays;
        $this->logRepository = $logRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->enabled === false) {
            return;
        }

        $this->logRepository->deleteExpired($this->expirationDays);
    }
}
