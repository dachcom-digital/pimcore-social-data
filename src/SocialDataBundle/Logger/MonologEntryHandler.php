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
        $logEntry = $this->logManager->createNewForConnector($record['context']['connector']);

        $logEntry->setMessage($record['message']);
        $logEntry->setType($record['level']);

        $this->logManager->update($logEntry);
    }
}