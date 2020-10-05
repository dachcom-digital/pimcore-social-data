<?php

namespace SocialDataBundle\Builder;

use SocialDataBundle\Connector\ConnectorEngineConfigurationInterface;
use SocialDataBundle\Connector\ConnectorFeedConfigurationInterface;
use SocialDataBundle\Manager\ConnectorManagerInterface;
use SocialDataBundle\Manager\WallManagerInterface;
use SocialDataBundle\Model\ConnectorEngineInterface;
use SocialDataBundle\Model\WallInterface;
use Pimcore\Translation\Translator;
use SocialDataBundle\Service\StatisticServiceInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ExtJsDataBuilder
{
    /**
     * @var NormalizerInterface
     */
    protected $serializer;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var ConnectorManagerInterface
     */
    protected $connectorManager;

    /**
     * @var WallManagerInterface
     */
    protected $wallManager;

    /**
     * @var StatisticServiceInterface
     */
    protected $statisticService;

    /**
     * @param NormalizerInterface       $serializer
     * @param Translator                $translator
     * @param ConnectorManagerInterface $connectorManager
     * @param WallManagerInterface      $wallManager
     * @param StatisticServiceInterface $statisticService
     */
    public function __construct(
        NormalizerInterface $serializer,
        Translator $translator,
        ConnectorManagerInterface $connectorManager,
        WallManagerInterface $wallManager,
        StatisticServiceInterface $statisticService
    ) {
        $this->serializer = $serializer;
        $this->translator = $translator;
        $this->connectorManager = $connectorManager;
        $this->wallManager = $wallManager;
        $this->statisticService = $statisticService;
    }

    /**
     * @return array
     */
    public function generateConnectorListData()
    {
        $connectors = [];
        $allConnectorDefinitions = $this->connectorManager->getAllConnectorDefinitions(true);

        foreach ($allConnectorDefinitions as $connectorDefinitionName => $connectorDefinition) {

            $engineConfiguration = [];
            $isInstalled = $connectorDefinition->engineIsLoaded();

            if ($isInstalled === true && $connectorDefinition->needsEngineConfiguration()) {
                $engineConfiguration = $this->serializer->normalize($connectorDefinition->getConnectorEngine(), 'array', $this->getExtJSSerializerContext());
            }

            $connectors[] = [
                'name'   => $connectorDefinitionName,
                'label'  => ucfirst($connectorDefinitionName),
                'config' => [
                    'installed'           => $isInstalled,
                    'enabled'             => $isInstalled && $connectorDefinition->getConnectorEngine()->isEnabled(),
                    'connected'           => $isInstalled && $connectorDefinition->isConnected(),
                    'autoConnect'         => $connectorDefinition->isAutoConnected(),
                    'customConfiguration' => $engineConfiguration['configuration'] ?: []
                ]
            ];
        }

        return $connectors;
    }

    /**
     * @return array
     */
    public function generateWallListData()
    {
        $walls = $this->wallManager->getAll();

        $items = [];

        foreach ($walls as $wall) {
            $items[] = [
                'id'            => (int) $wall->getId(),
                'text'          => $wall->getName(),
                'icon'          => '',
                'leaf'          => true,
                'iconCls'       => 'social_data_wall_icon',
                'allowChildren' => false
            ];
        }

        return $items;
    }

    /**
     * @param WallInterface $wall
     *
     * @return array
     */
    public function generateWallDetailData(WallInterface $wall)
    {
        $feeds = $this->serializer->normalize($wall->getFeeds(), 'array', $this->getExtJSSerializerContext());

        $statisticData = [];
        foreach ($this->statisticService->getWallStatistics($wall) as $identifier => $value) {
            $statisticData[] = [
                'label' => sprintf('social_data.statistic.%s', $identifier),
                'value' => $value
            ];
        }

        $data = [
            'id'           => $wall->getId(),
            'name'         => $wall->getName(),
            'dataStorage'  => $wall->getDataStorage(),
            'assetStorage' => $wall->getAssetStorage(),
            'statistics'   => $statisticData,
            'feeds'        => array_values($feeds)
        ];

        $data['stores'] = [
            'feedStore' => $this->generateFeedStore()
        ];;

        return $data;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function getSaveName(string $name)
    {
        return (string) preg_replace('/[^A-Za-z0-9aäüöÜÄÖß \-]/', '', $name);
    }

    /**
     * @param FormInterface $form
     *
     * @return array
     */
    public function generateFormErrorList(FormInterface $form)
    {
        $errors = [];

        /** @var FormError $e */
        foreach ($form->getErrors(true, true) as $e) {
            $errorMessageTemplate = $e->getMessageTemplate();
            foreach ($e->getMessageParameters() as $key => $value) {
                $errorMessageTemplate = str_replace($key, $value, $errorMessageTemplate);
            }
            $errors[] = sprintf('%s: %s', $e->getOrigin()->getConfig()->getName(), $errorMessageTemplate);
        }

        return $errors;
    }

    /**
     * @return array
     */
    protected function generateFeedStore()
    {
        $feedStore = [];
        foreach ($this->connectorManager->getAllActiveConnectorDefinitions() as $connectorDefinitionName => $connectorDefinition) {
            $feedStore[] = [
                'label'           => ucFirst($connectorDefinitionName),
                'connectorName'   => ucFirst($connectorDefinitionName),
                'connectorEngine' => $connectorDefinition->getConnectorEngine()->getId(),
                'iconCls'         => sprintf('pimcore_icon_social_data_connector_%s', strtolower($connectorDefinitionName))
            ];
        }

        return $feedStore;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function translate($value)
    {
        if (empty($value)) {
            return $value;
        }

        return $this->translator->trans($value, [], 'admin');
    }

    /**
     * @return array
     */
    protected function getExtJSSerializerContext()
    {
        return [
            'groups'                      => ['ExtJs'],
            AbstractNormalizer::CALLBACKS => [
                'connectorEngine' => function ($data) {
                    return $data instanceof ConnectorEngineInterface ? $data->getId() : null;
                },
                'configuration'   => function ($data) {
                    if ($data instanceof ConnectorFeedConfigurationInterface) {
                        return $this->serializer->normalize($data);
                    } elseif ($data instanceof ConnectorEngineConfigurationInterface) {
                        return $this->serializer->normalize($data);
                    }

                    return [];
                }
            ]
        ];
    }
}
