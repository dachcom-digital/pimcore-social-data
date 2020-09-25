<?php

namespace SocialDataBundle\Controller\Admin\Connector;

use Facebook\Exceptions\FacebookSDKException;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use SocialDataBundle\Connector\Facebook\Api\FacebookClient;
use SocialDataBundle\Service\ConnectorServiceInterface;
use SocialDataBundle\Connector\Facebook\Model\EngineConfiguration;
use SocialDataBundle\Service\EnvironmentServiceInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FacebookController extends AdminController
{
    /**
     * @var FacebookClient
     */
    protected $facebookClient;

    /**
     * @var EnvironmentServiceInterface
     */
    protected $environmentService;

    /**
     * @var ConnectorServiceInterface
     */
    protected $connectorService;

    /**
     * @param FacebookClient              $facebookClient
     * @param EnvironmentServiceInterface $environmentService
     * @param ConnectorServiceInterface   $connectorService
     */
    public function __construct(
        FacebookClient $facebookClient,
        EnvironmentServiceInterface $environmentService,
        ConnectorServiceInterface $connectorService
    ) {
        $this->facebookClient = $facebookClient;
        $this->environmentService = $environmentService;
        $this->connectorService = $connectorService;
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @throws FacebookSDKException
     */
    public function connectAction(Request $request)
    {
        $connectorDefinition = $this->connectorService->getConnectorDefinition('facebook', true);

        if (!$connectorDefinition->engineIsLoaded()) {
            throw $this->createNotFoundException('Not Found');
        }

        $connectorEngineConfig = $connectorDefinition->getEngineConfiguration();
        if (!$connectorEngineConfig instanceof EngineConfiguration) {
            throw new HttpException(400, 'Invalid facebook configuration. Please configure your connector "facebook" in backend first.');
        }

        $fb = $this->facebookClient->getClient($connectorEngineConfig);
        $helper = $fb->getRedirectLoginHelper();

        $callbackUrl = $this->generateUrl('social_data_connector_connect_check', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $permissions = ['pages_show_list'];

        $loginUrl = $helper->getLoginUrl($callbackUrl, $permissions);

        return $this->redirect($loginUrl);
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function checkAction(Request $request)
    {
        $connectorDefinition = $this->connectorService->getConnectorDefinition('facebook', true);

        if (!$connectorDefinition->engineIsLoaded()) {
            throw $this->createNotFoundException('Not Found');
        }

        $connectorEngineConfig = $connectorDefinition->getEngineConfiguration();
        if (!$connectorEngineConfig instanceof EngineConfiguration) {
            throw new HttpException(400, 'Invalid facebook configuration. Please configure your connector "facebook" in backend first.');
        }

        $fb = $this->facebookClient->getClient($connectorEngineConfig);
        $helper = $fb->getRedirectLoginHelper();

        if (!$accessToken = $helper->getAccessToken()) {
            if ($helper->getError()) {
                throw new HttpException(400, $helper->getError());
            } else {
                throw new HttpException(400, $request->query->get('error_message', 'Unknown Error.'));
            }
        }

        try {
            $oAuth2Client = $fb->getOAuth2Client();
            $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
        } catch (FacebookSDKException $e) {
            throw new HttpException(400, $e->getMessage());
        }

        $connectorEngineConfig->setAccessToken($accessToken->getValue());
        $connectorEngineConfig->setAccessTokenExpiresAt($accessToken->getExpiresAt());
        $this->connectorService->updateConnectorEngineConfiguration('facebook', $connectorEngineConfig);

        $response = new Response();
        $response->setContent('Successfully connected. You can now close this window and return to backend to complete the configuration.');

        return $response;
    }
}
