<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

namespace SocialDataBundle\Controller\Admin;

use Pimcore\Bundle\AdminBundle\Controller\AdminAbstractController;
use SocialDataBundle\Builder\ExtJsDataBuilder;
use SocialDataBundle\Connector\ConnectorEngineConfigurationInterface;
use SocialDataBundle\Manager\ConnectorManagerInterface;
use SocialDataBundle\Model\SocialPostInterface;
use SocialDataBundle\Registry\ConnectorDefinitionRegistryInterface;
use SocialDataBundle\Service\ConnectorServiceInterface;
use SocialDataBundle\Service\EnvironmentServiceInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SettingsController extends AdminAbstractController
{
    public function __construct(
        protected FormFactoryInterface $formFactory,
        protected EnvironmentServiceInterface $environmentService,
        protected ConnectorManagerInterface $connectorManager,
        protected ConnectorDefinitionRegistryInterface $connectorRegistry,
        protected ConnectorServiceInterface $connectorService,
        protected ExtJsDataBuilder $extJsDataBuilder
    ) {
    }

    /**
     * @throws \Exception
     */
    public function getConnectorsAction(Request $request): JsonResponse
    {
        $connectors = $this->extJsDataBuilder->generateConnectorListData();

        return $this->adminJson([
            'success'    => true,
            'connectors' => $connectors
        ]);
    }

    public function getConnectorAction(Request $request, string $connectorName): JsonResponse
    {
        $connector = $this->extJsDataBuilder->generateConnectorData($connectorName);

        return $this->adminJson([
            'success'   => true,
            'connector' => $connector
        ]);
    }

    public function dataClassHealthCheckAction(Request $request): JsonResponse
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

    public function installConnectorAction(Request $request, string $connectorName): JsonResponse
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

    public function uninstallConnectorAction(Request $request, string $connectorName): JsonResponse
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

    public function changeConnectorStateAction(Request $request, string $connectorName, string $stateType, string $flag = 'activate'): JsonResponse
    {
        $success = true;
        $message = null;

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
        }

        return $this->adminJson([
            'success'   => $success,
            'message'   => $message,
            'stateMode' => $stateMode
        ]);
    }

    public function saveConnectorConfigurationAction(Request $request, string $connectorName): JsonResponse
    {
        $configuration = json_decode($request->request->get('configuration'), true);

        $connectorDefinition = $this->connectorManager->getConnectorDefinition($connectorName, true);

        $formType = '';
        $class = $connectorDefinition->getEngineConfigurationClass();
        if (is_string($class) && is_subclass_of($class, ConnectorEngineConfigurationInterface::class)) {
            $formType = $class::getFormClass();
        }

        $form = $this->formFactory->create($formType, $connectorDefinition->getEngineConfiguration());

        $form->submit($configuration);

        if (!$form->isValid()) {
            return $this->adminJson([
                'success' => false,
                'message' => sprintf(
                    'Error while processing backend configuration for %s":<br>%s',
                    $connectorName,
                    join('<br>', $this->extJsDataBuilder->generateFormErrorList($form))
                )
            ]);
        }

        /** @var ConnectorEngineConfigurationInterface $configuration */
        $connectorConfiguration = $form->getData();

        $this->connectorService->updateConnectorEngineConfiguration($connectorName, $connectorConfiguration);

        return $this->adminJson([
            'success'   => true,
            'message'   => null,
            'connector' => $this->extJsDataBuilder->generateConnectorData($connectorName)
        ]);
    }
}
