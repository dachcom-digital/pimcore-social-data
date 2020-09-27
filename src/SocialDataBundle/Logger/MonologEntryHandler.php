<?php

namespace SocialDataBundle\Logger;

use Monolog\Handler\AbstractProcessingHandler;
use SocialDataBundle\Manager\LogManagerInterface;

class MonologEntryHandler extends AbstractProcessingHandler
{
    /**
     * @var LogManagerInterface
     */
    protected $logManager;

    /**
     * @param LogManagerInterface $logManager
     */
    public function setLogManager(LogManagerInterface $logManager)
    {
        $this->logManager = $logManager;
    }

    /**
     * @param array $record
     */
    protected function write(array $record)
    {
        $context = is_array($record['context']) ? $record['context'] : [];
        $logEntry = $this->logManager->createNewForConnector($context);

        $logEntry->setMessage($record['message']);
        $logEntry->setType($record['level']);

        $this->logManager->update($logEntry);
    }
}