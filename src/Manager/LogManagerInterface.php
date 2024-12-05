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

namespace SocialDataBundle\Manager;

use Doctrine\ORM\Tools\Pagination\Paginator;
use SocialDataBundle\Model\LogEntryInterface;

interface LogManagerInterface
{
    public function getForConnectorEngine(int $connectorEngineId): Paginator;

    public function getForWall(int $wallId): Paginator;

    /**
     * @throws \Exception
     */
    public function flushLogs(): void;

    public function createNew(): LogEntryInterface;

    public function createNewForConnector(array $context): LogEntryInterface;

    public function update(LogEntryInterface $logEntry): void;

    public function delete(LogEntryInterface $logEntry): void;
}
