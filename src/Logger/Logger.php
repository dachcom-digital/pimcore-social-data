<?php

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
