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

class Logger implements LoggerInterface
{
    public function __construct(protected \Psr\Log\LoggerInterface $logger)
    {
    }

    public function log(string $level, $message, ?array $context = null): void
    {
        $this->logger->log($level, $message, is_array($context) ? $context : []);
    }

    public function debug(string $message, ?array $context = null): void
    {
        $this->log('DEBUG', $message, is_array($context) ? $context : []);
    }

    public function info(string $message, ?array $context = null): void
    {
        $this->log('INFO', $message, is_array($context) ? $context : []);
    }

    public function warning(string $message, ?array $context = null): void
    {
        $this->log('WARNING', $message, is_array($context) ? $context : []);
    }

    public function error(string $message, ?array $context = null): void
    {
        $this->log('ERROR', $message, is_array($context) ? $context : []);
    }
}
