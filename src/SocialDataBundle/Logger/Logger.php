<?php

namespace SocialDataBundle\Logger;

class Logger implements LoggerInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function log($level, $message, string $connector)
    {
        $this->logger->log($level, $message, ['connector' => $connector]);
    }

    /**
     * {@inheritdoc}
     */
    public function debug($message, string $connector)
    {
        $this->log('DEBUG', $message, $connector);
    }

    /**
     * {@inheritdoc}
     */
    public function info($message, string $connector)
    {
        $this->log('INFO', $message, $connector);
    }

    /**
     * {@inheritdoc}
     */
    public function warning($message, string $connector)
    {
        $this->log('WARNING', $message, $connector);
    }

    /**
     * {@inheritdoc}
     */
    public function error($message, string $connector)
    {
        $this->log('ERROR', $message, $connector);
    }
}
