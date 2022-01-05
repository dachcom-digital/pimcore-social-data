<?php

namespace SocialDataBundle\Logger;

interface LoggerInterface
{
    /**
     * DEBUG (100)
     * INFO (200)
     * NOTICE (250)
     * WARNING (300)
     * ERROR (400)
     * CRITICAL (500)
     * ALERT (550)
     * EMERGENCY (600)
     */
    public function log(string $level, string $message, ?array $context = null): void;

    public function debug(string $message, ?array $context = null): void;

    public function info(string $message, ?array $context = null): void;

    public function warning(string $message, ?array $context = null): void;

    public function error(string $message, ?array $context = null): void;
}
