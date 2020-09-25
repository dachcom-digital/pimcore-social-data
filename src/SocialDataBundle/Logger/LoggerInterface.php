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
     * @param string $level
     * @param string $message
     * @param string $connector
     */
    public function log(string $level, string $message, string $connector);

    /**
     * @param string $message
     * @param string $connector
     */
    public function debug($message, string $connector);

    /**
     * @param string $message
     * @param string $connector
     */
    public function info($message, string $connector);

    /**
     * @param string $message
     * @param string $connector
     */
    public function warning($message, string $connector);

    /**
     * @param string $message
     * @param string $connector
     */
    public function error($message, string $connector);
}
