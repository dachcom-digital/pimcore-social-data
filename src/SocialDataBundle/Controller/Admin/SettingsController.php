<?php

namespace SocialDataBundle\Controller\Admin;

use SocialDataBundle\Builder\ExtJsDataBuilder;
use SocialDataBundle\Connector\ConnectorEngineConfigurationInterface;
use SocialDataBundle\Form\Admin\Type\Wall\WallType;
use SocialDataBundle\Service\ConnectorServiceInterface;
use SocialDataBundle\Service\EnvironmentServiceInterface;
use SocialDataBundle\Manager\ConnectorManagerInterface;
use SocialDataBundle\Model\SocialPostInterface;
use SocialDataBundle\Registry\ConnectorDefinitionRegistryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;

class SettingsController extends AdminController
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var EnvironmentServiceInterface
     */
    protected $environmentService;

    /**
     * @var ConnectorManagerInterface
     */
    protected $connectorManager;

    /**
     * @var ConnectorDefinitionRegistryInterface
     */
    protected $connectorRegistry;

    /**
     * @var ConnectorServiceInterface
     */
    protected $connectorService;

    /**
     * @var ExtJsDataBuilder
     */
    protected $extJsDataBuilder;

    /**
     * @param FormFactoryInterface                 $formFactory
     * @param EnvironmentServiceInterface          $environmentService
     * @param ConnectorManagerInterface            $connectorManager
     * @param ConnectorDefinitionRegistryInterface $connectorRegistry
     * @param ConnectorServiceInterface            $connectorService
     * @param ExtJsDataBuilder                     $extJsDataBuilder
     */
    public function __construct(

        FormFactoryInterface $formFactory,
        EnvironmentServiceInterface $environmentService,
        ConnectorManagerInterface $connectorManager,
        ConnectorDefinitionRegistryInterface $connectorRegistry,
        ConnectorServiceInterface $connectorService,
        ExtJsDataBuilder $extJsDataBuilder
    ) {
        $this->formFactory = $formFactory;
        $this->environmentService = $environmentService;
        $this->connectorManager = $connectorManager;
        $this->connectorRegistry = $connectorRegistry;
        $this->connectorService = $connectorService;
        $this->extJsDataBuilder = $extJsDataBuilder;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function getConnectorsAction(Request $request)
    {
        $connectors = $this->extJsDataBuilder->generateConnectorListData();

        return $this->adminJson([
            'success'    => true,
            'connectors' => $connectors
        ]);
    }

    /**
     * @param Request $request
     * @param string  $connectorName
     *
     * @return JsonResponse
     *
     */
    public function getConnectorAction(Request $request, string $connectorName)
    {
        $connector = $this->extJsDataBuilder->generateConnectorData($connectorName);

        return $this->adminJson([
            'success'   => true,
            'connector' => $connector
        ]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function dataClassHealthCheckAction(Request $request)
    {
        $dataClassReady = false;
        $dataClass = $this->environmentService->getSocialPostDataClass();
        $dataClassPath = sprintf('Pimcore\Model\DataObject\%s', ucfirst($dataClass));

        if (class_exists($dataClassPath) && in_array(SocialPostInterface::class, class_implements($dataClassPath))) {
            $dataClassReady = true;
        }

        return $this->adminJson([
            'success'        => true,
            'dataClassPath'  => $dataClassPath,
            'dataClassReady' => $dataClassReady
        ]);
    }

    /**
     * @param Request $request
     * @param string  $connectorName
     *
     * @return JsonResponse
     */
    public function installConnectorAction(Request $request, string $connectorName)
    {
        $success = true;
        $message = null;
        $installed = false;
        $connectorEngineId = null;

        try {
            $connectorEngine = $this->connectorService->installConnector($connectorName);
            $connectorEngineId = $connectorEngine->getId();
            $installed = true;
        } catch (\Throwable $e) {
            $success = false;
            $message = $e->getMessage();
        }

        return $this->adminJson([
            'success'           => $success,
            'message'           => $message,
            'installed'         => $installed,
            'connectorEngineId' => $connectorEngineId
        ]);
    }

    /**
     * @param Request $request
     * @param string  $connectorName
     *
     * @return JsonResponse
     */
    public function uninstallConnectorAction(Request $request, string $connectorName)
    {
        $success = true;
        $message = null;
        $installed = true;

        try {
            $this->connectorService->uninstallConnector($connectorName);
            $installed = false;
        } catch (\Throwable $e) {
            $success = false;
            $message = $e->getMessage();
        }

        return $this->adminJson([
            'success'   => $success,
            'message'   => $message,
            'installed' => $installed
        ]);
    }

    /**
     * @param Request $request
     * @param string  $connectorName
     * @param string  $stateType
     * @param string  $flag
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function changeConnectorStateAction(Request $request, string $connectorName, string $stateType, string $flag = 'activate')
    {
        $success = true;
        $message = null;
        $stateMode = null;

        switch ($stateType) {
            case 'availability':
                try {
                    if ($flag === 'activate') {
                        $stateMode = 'activated';
                        $this->connectorService->enableConnector($connectorName);
                    } else {
                        $stateMode = 'deactivated';
                        $this->connectorService->disableConnector($connectorName);
                    }
                } catch (\Exception $e) {
                    $success = false;
                    $message = $e->getMessage();
                }

                break;
            case 'connection':
                try {
                    if ($flag === 'activate') {
                        $stateMode = 'activated';
                        $this->connectorService->connectConnector($connectorName);
                    } else {
                        $stateMode = 'deactivated';
                        $this->connectorService->disconnectConnector($connectorName);
                    }
                } catch (\Exception $e) {
                    $success = false;
                    $message = $e->getMessage();
                }

                break;
            default:
                throw new \Exception(sprintf('Invalid state type "%s"', $stateType));

                break;
        }

        return $this->adminJson([
            'success'   => $success,
            'message'   => $message,
            'stateMode' => $stateMode
        ]);
    }

    /**
     * @param Request $request
     * @param string  $connectorName
     *
     * @return JsonResponse
     */
    public function saveConnectorConfigurationAction(Request $request, string $connectorName)
    {
        $configuration = json_decode($request->request->get('configuration'), true);

        $connectorDefinition = $this->connectorManager->getConnectorDefinition($connectorName, true);

        /** @var ConnectorEngineConfigurationInterface $class */
        $class = $connectorDefinition->getEngineConfigurationClass();

        $form = $this->formFactory->create($class::getFormClass(), $connectorDefinition->getEngineConfiguration());

        $form->submit($configuration);

        if (!$form->isValid()) {
            return $this->adminJson([
                'success' => false,
                'message' => sprintf('Error while processing backend configuration for %s":<br>%s',
                    $connectorName, join('<br>', $this->extJsDataBuilder->generateFormErrorList($form))
                )
            ]);
        }

        /** @var ConnectorEngineConfigurationInterface $configuration */
        $connectorConfiguration = $form->getData();

        $this->connectorService->updateConnectorEngineConfiguration($connectorName, $connectorConfiguration);

        return $this->adminJson([
            'success' => true,
            'message' => null,
            'connector' => $this->extJsDataBuilder->generateConnectorData($connectorName)
        ]);
    }
}
