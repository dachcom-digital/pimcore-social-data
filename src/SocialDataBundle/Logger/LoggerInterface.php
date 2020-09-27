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
     *
     * @param string     $level
     * @param string     $message
     * @param array|null $context
     */
    public function log(string $level, string $message, ?array $context = null);

    /**
     * @param string     $message
     * @param array|null $context
     */
    public function debug($message, ?array $context = null);

    /**
     * @param string     $message
     * @param array|null $context
     */
    public function info($message, ?array $context = null);

    /**
     * @param string     $message
     * @param array|null $context
     */
    public function warning($message, ?array $context = null);

    /**
     * @param string     $message
     * @param array|null $context
     */
    public function error($message, ?array $context = null);
}
