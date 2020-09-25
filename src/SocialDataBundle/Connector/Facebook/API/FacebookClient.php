<?php

namespace SocialDataBundle\Connector\Facebook\Api;

use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook as FacebookSDK;
use SocialDataBundle\Connector\Facebook\API\Session\FacebookDataHandler;
use SocialDataBundle\Connector\Facebook\Model\EngineConfiguration;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FacebookClient
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @param EngineConfiguration $configuration
     *
     * @return FacebookSDK
     *
     * @throws FacebookSDKException
     */
    public function getClient(EngineConfiguration $configuration)
    {
        return new FacebookSDK([
            'app_id'                  => $configuration->getAppId(),
            'app_secret'              => $configuration->getAppSecret(),
            'persistent_data_handler' => new FacebookDataHandler($this->session),
            'default_graph_version'   => 'v2.8'
        ]);
    }
}
