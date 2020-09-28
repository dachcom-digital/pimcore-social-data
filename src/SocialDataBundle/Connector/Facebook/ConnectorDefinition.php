<?php

namespace SocialDataBundle\Connector\Facebook;

use SocialDataBundle\Connector\ConnectorEngineConfigurationInterface;
use SocialDataBundle\Connector\ConnectorDefinitionInterface;
use SocialDataBundle\Connector\Facebook\Model\EngineConfiguration;
use SocialDataBundle\Connector\Facebook\Model\FeedConfiguration;
use SocialDataBundle\Connector\SocialPostBuilderInterface;
use SocialDataBundle\Model\ConnectorEngineInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConnectorDefinition implements ConnectorDefinitionInterface
{
    /**
     * @var ConnectorEngineInterface|null
     */
    protected $connectorEngine;

    /**
     * @var SocialPostBuilderInterface
     */
    protected $socialPostBuilder;

    /**
     * @var array
     */
    protected $definitionConfiguration;

    /**
     * {@inheritdoc}
     */
    public function setConnectorEngine(?ConnectorEngineInterface $connectorEngine)
    {
        $this->connectorEngine = $connectorEngine;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnectorEngine()
    {
        return $this->connectorEngine;
    }

    /**
     * {@inheritdoc}
     */
    public function setSocialPostBuilder(SocialPostBuilderInterface $builder)
    {
        $this->socialPostBuilder = $builder;
    }

    /**
     * {@inheritdoc}
     */
    public function getSocialPostBuilder()
    {
        return $this->socialPostBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefinitionConfiguration(array $definitionConfiguration)
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'core_disabled' => false
        ]);

        $resolver->setAllowedTypes('core_disabled', 'bool');

        try {
            $this->definitionConfiguration = $resolver->resolve($definitionConfiguration);
        } catch (\Throwable $e) {
            throw new \Exception(sprintf('Invalid "%s" connector configuration. %s', 'facebook', $e->getMessage()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinitionConfiguration()
    {
        return $this->definitionConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function engineIsLoaded()
    {
        return $this->getConnectorEngine() instanceof ConnectorEngineInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function isOnline()
    {
        if ($this->definitionConfiguration['core_disabled'] === true) {
            return false;
        }

        if (!$this->engineIsLoaded()) {
            return false;
        }

        if ($this->getConnectorEngine()->isEnabled() === false) {
            return false;
        }

        if (!$this->isConnected()) {
            return false;
        }

        $configuration = $this->getConnectorEngine()->getConfiguration();
        if (!$configuration instanceof EngineConfiguration) {
            return false;
        }

        return $this->isConnected();
    }

    /**
     * {@inheritdoc}
     */
    public function beforeEnable()
    {
        // not required. just enable it.
    }

    /**
     * {@inheritdoc}
     */
    public function beforeDisable()
    {
        // not required. just disable it.
    }

    /**
     * {@inheritdoc}
     */
    public function isAutoConnected()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isConnected()
    {
        if ($this->engineIsLoaded() === false) {
            return false;
        }

        $configuration = $this->getConnectorEngine()->getConfiguration();
        if (!$configuration instanceof EngineConfiguration) {
            return false;
        }

        return $configuration->getAccessToken() !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        if ($this->isConnected() === false) {
            throw new \Exception('No valid Access Token found. If you already tried to connect your application check your credentials again.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect()
    {
        // @todo
    }

    /**
     * {@inheritdoc}
     */
    public function needsEngineConfiguration()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function hasLogPanel()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getEngineConfigurationClass()
    {
        return EngineConfiguration::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getFeedConfigurationClass()
    {
        return FeedConfiguration::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getEngineConfiguration()
    {
        if (!$this->engineIsLoaded()) {
            return null;
        }

        $engineConfiguration = $this->getConnectorEngine()->getConfiguration();
        if (!$engineConfiguration instanceof ConnectorEngineConfigurationInterface) {
            return null;
        }

        return $engineConfiguration;
    }
}
