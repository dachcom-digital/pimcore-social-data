<?php

namespace SocialDataBundle\Connector\Facebook;

use SocialDataBundle\Connector\ConnectorEngineConfigurationInterface;

class EngineConfiguration implements ConnectorEngineConfigurationInterface
{
    /**
     * @internal
     *
     * @var string
     */
    protected $accessToken;

    /**
     * @internal
     *
     * @var string
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
     * @param string $token
     *
     * @internal
     */
    public function setAccessToken($token)
    {
        $this->accessToken = $token;
    }

    /**
     * @param string $expiresAt
     *
     * @internal
     */
    public function setAccessTokenExpiresAt($expiresAt)
    {
        $this->accessTokenExpiresAt = $expiresAt;
    }

    /**
     * @return string
     *
     * @internal
     */
    public function getAccessToken()
    {
        return $this->accessToken;
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

    /**
     * {@inheritdoc}
     */
    public function getConfigParam(string $param)
    {
        $getter = sprintf('get%s', ucfirst($param));
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function toBackendConfigArray()
    {
        return [
            'appId'     => $this->getAppId(),
            'appSecret' => $this->getAppSecret()
        ];
    }
}
