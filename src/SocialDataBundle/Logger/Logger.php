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
    public function log($level, $message, ?array $context = null)
    {
        $this->logger->log($level, $message, is_array($context) ? $context : []);
    }

    /**
     * {@inheritdoc}
     */
    public function debug($message, ?array $context = null)
    {
        $this->log('DEBUG', $message, is_array($context) ? $context : []);
    }

    /**
     * {@inheritdoc}
     */
    public function info($message, ?array $context = null)
    {
        $this->log('INFO', $message, is_array($context) ? $context : []);
    }

    /**
     * {@inheritdoc}
     */
    public function warning($message, ?array $context = null)
    {
        $this->log('WARNING', $message, is_array($context) ? $context : []);
    }

    /**
     * {@inheritdoc}
     */
    public function error($message, ?array $context = null)
    {
        $this->log('ERROR', $message, is_array($context) ? $context : []);
    }
}
