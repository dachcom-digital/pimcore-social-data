<?php

namespace SocialDataBundle\Logger;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;
use SocialDataBundle\Manager\LogManagerInterface;

class MonologEntryHandler extends AbstractProcessingHandler
{
    protected LogManagerInterface $logManager;

    public function setLogManager(LogManagerInterface $logManager): void
    {
        $this->logManager = $logManager;
    }

    protected function write(LogRecord $record): void
    {
        $context = $record['context'];
        $logEntry = $this->logManager->createNewForConnector($context);

        $logEntry->setMessage($record['message']);
        $logEntry->setType($record['level']);

        $this->logManager->update($logEntry);
    }
}