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
        $connectorEngineConfig = $this->getConnectorEngineConfig();

        $fb = $this->facebookClient->getClient($connectorEngineConfig);
        $helper = $fb->getRedirectLoginHelper();

        $callbackUrl = $this->generateUrl('social_data_connector_connect_check', [], UrlGeneratorInterface::ABSOLUTE_URL);

        // @todo: make this configurable (e.g. via connector config?)
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
        $connectorEngineConfig = $this->getConnectorEngineConfig();

        $fb = $this->facebookClient->getClient($connectorEngineConfig);
        $helper = $fb->getRedirectLoginHelper();

        if (!$accessToken = $helper->getAccessToken()) {

            if ($helper->getError()) {
                return $this->render('@SocialData/connect-layout.html.twig', [
                    'content' => [
                        'error'       => true,
                        'code'        => $helper->getErrorCode(),
                        'identifier'  => $helper->getError(),
                        'reason'      => $helper->getErrorReason(),
                        'description' => $helper->getErrorDescription()
                    ]
                ]);
            }

            return $this->render('@SocialData/connect-layout.html.twig', [
                'content' => [
                    'error'       => true,
                    'code'        => 500,
                    'identifier'  => 'general_error',
                    'reason'      => 'invalid access token',
                    'description' => $request->query->get('error_message', 'Unknown Error')
                ]
            ]);
        }

        try {
            $oAuth2Client = $fb->getOAuth2Client();
            $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
        } catch (FacebookSDKException $e) {
            return $this->render('@SocialData/connect-layout.html.twig', [
                'content' => [
                    'error'       => true,
                    'code'        => 500,
                    'identifier'  => 'general_error',
                    'reason'      => 'long lived access token error',
                    'description' => $e->getMessage()
                ]
            ]);
        }

        $connectorEngineConfig->setAccessToken($accessToken->getValue());
        $connectorEngineConfig->setAccessTokenExpiresAt($accessToken->getExpiresAt());
        $this->connectorService->updateConnectorEngineConfiguration('facebook', $connectorEngineConfig);

        return $this->render('@SocialData/connect-layout.html.twig', [
            'content' => [
                'error'   => false
            ]
        ]);
    }

    /**
     * @return EngineConfiguration
     */
    protected function getConnectorEngineConfig()
    {
        $connectorDefinition = $this->connectorService->getConnectorDefinition('facebook', true);

        if (!$connectorDefinition->engineIsLoaded()) {
            throw $this->createNotFoundException('Not Found');
        }

        $connectorEngineConfig = $connectorDefinition->getEngineConfiguration();
        if (!$connectorEngineConfig instanceof EngineConfiguration) {
            throw new HttpException(400, 'Invalid facebook configuration. Please configure your connector "facebook" in backend first.');
        }

        return $connectorEngineConfig;
    }
}
