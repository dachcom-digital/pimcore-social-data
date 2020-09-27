<?php

namespace SocialDataBundle\EventListener\Maintenance;

use SocialDataBundle\Repository\LogRepositoryInterface;
use Pimcore\Maintenance\TaskInterface;

class CleanUpLogsTask implements TaskInterface
{
    /**
     * @var int
     */
    protected $logExpirationDays;

    /**
     * @var LogRepositoryInterface
     */
    protected $logRepository;

    /**
     * @param int                    $logExpirationDays
     * @param LogRepositoryInterface $logRepository
     */
    public function __construct(int $logExpirationDays, LogRepositoryInterface $logRepository)
    {
        $this->logExpirationDays = $logExpirationDays;
        $this->logRepository = $logRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->logRepository->deleteExpired($this->logExpirationDays);
    }
}
