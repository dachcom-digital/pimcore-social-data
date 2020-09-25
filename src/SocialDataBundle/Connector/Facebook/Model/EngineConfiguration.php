<?php

namespace SocialDataBundle\Connector\Facebook\Model;

use SocialDataBundle\Connector\ConnectorEngineConfigurationInterface;
use SocialDataBundle\Connector\Facebook\Admin\Form\FacebookEngineType;

class EngineConfiguration implements ConnectorEngineConfigurationInterface
{
    /**
     * @var string
     *
     * @internal
     */
    protected $accessToken;

    /**
     * @var string
     *
     * @internal
     */
    protected $accessTokenExpiresAt;

    /**
     * @var string
     */
    protected $appId;

    /**
     * @var string
     */
    protected $appSecret;

    /**
     * {@inheritdoc}
     */
    public static function getFormClass()
    {
        return FacebookEngineType::class;
    }

    /**
     * @param string $token
     * @param bool   $forceUpdate
     */
    public function setAccessToken($token, $forceUpdate = false)
    {
        // symfony: if there are any fields on the form that aren’t included in the submitted data,
        // those fields will be explicitly set to null.
        if ($token === null && $forceUpdate === false) {
            return;
        }

        $this->accessToken = $token;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param string $expiresAt
     * @param bool   $forceUpdate
     */
    public function setAccessTokenExpiresAt($expiresAt, $forceUpdate = false)
    {
        // symfony: if there are any fields on the form that aren’t included in the submitted data,
        // those fields will be explicitly set to null.
        if ($expiresAt === null && $forceUpdate === false) {
            return;
        }

        $this->accessTokenExpiresAt = $expiresAt;
    }

    /**
     * @return string
     */
    public function getAccessTokenExpiresAt()
    {
        return $this->accessTokenExpiresAt;
    }

    /**
     * @param string $appId
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;
    }

    /**
     * @return string
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @param string $appSecret
     */
    public function setAppSecret($appSecret)
    {
        $this->appSecret = $appSecret;
    }

    /**
     * @return string
     */
    public function getAppSecret()
    {
        return $this->appSecret;
    }
}
