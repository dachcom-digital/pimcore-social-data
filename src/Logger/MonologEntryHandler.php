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
