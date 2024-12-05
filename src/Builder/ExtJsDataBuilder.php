<?php

namespace SocialDataBundle\Builder;

use SocialDataBundle\Connector\ConnectorDefinitionInterface;
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
    public function __construct(
        protected NormalizerInterface $serializer,
        protected Translator $translator,
        protected ConnectorManagerInterface $connectorManager,
        protected WallManagerInterface $wallManager,
        protected StatisticServiceInterface $statisticService
    ) {
    }

    public function generateConnectorListData(): array
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
                'name'    => $connectorDefinitionName,
                'label'   => ucfirst($connectorDefinitionName),
                'iconCls' => sprintf('pimcore_icon_social_data_connector_%s', strtolower($connectorDefinitionName)),
                'config'  => [
                    'installed'           => $isInstalled,
                    'enabled'             => $isInstalled && $connectorDefinition->getConnectorEngine()->isEnabled(),
                    'connected'           => $isInstalled && $connectorDefinition->isConnected(),
                    'autoConnect'         => $connectorDefinition->isAutoConnected(),
                    'customConfiguration' => $engineConfiguration['configuration'] ?? []
                ]
            ];
        }

        return $connectors;
    }

    public function generateConnectorData(string $connectorName): array
    {
        $connectorDefinition = $this->connectorManager->getConnectorDefinition($connectorName, true);

        if (!$connectorDefinition instanceof ConnectorDefinitionInterface) {
            return [];
        }

        $engineConfiguration = [];
        $isInstalled = $connectorDefinition->engineIsLoaded();

        if ($isInstalled === true && $connectorDefinition->needsEngineConfiguration()) {
            $engineConfiguration = $this->serializer->normalize($connectorDefinition->getConnectorEngine(), 'array', $this->getExtJSSerializerContext());
        }

        return [
            'name'    => $connectorName,
            'label'   => ucfirst($connectorName),
            'iconCls' => sprintf('pimcore_icon_social_data_connector_%s', strtolower($connectorName)),
            'config'  => [
                'installed'           => $isInstalled,
                'enabled'             => $isInstalled && $connectorDefinition->getConnectorEngine()->isEnabled(),
                'connected'           => $isInstalled && $connectorDefinition->isConnected(),
                'autoConnect'         => $connectorDefinition->isAutoConnected(),
                'customConfiguration' => $engineConfiguration['configuration'] ?: []
            ]
        ];
    }

    public function generateWallListData(): array
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

    public function generateWallDetailData(WallInterface $wall): array
    {
        $feeds = $this->serializer->normalize($wall->getFeeds(), 'array', $this->getExtJSSerializerContext());
        $wallTags = $this->serializer->normalize($wall->getWallTags(), 'array');

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
            'wallTags'     => array_values($wallTags),
            'feeds'        => array_values($feeds),
            'statistics'   => $statisticData,
        ];

        $data['stores'] = [
            'feedStore' => $this->generateFeedStore()
        ];

        return $data;
    }

    public function generateTagList(string $type): array
    {
        $tags = $this->wallManager->getAvailableTags($type);

        return $this->serializer->normalize($tags, 'array');
    }

    public function getSaveName(string $name): string
    {
        return (string) preg_replace('/[^A-Za-z0-9aäüöÜÄÖß \-]/u', '', $name);
    }

    public function generateFormErrorList(FormInterface $form): array
    {
        $errors = [];

        foreach ($form->getErrors(true, true) as $e) {

            if (!$e instanceof FormError) {
                continue;
            }

            $errorMessageTemplate = $e->getMessageTemplate();
            foreach ($e->getMessageParameters() as $key => $value) {
                $errorMessageTemplate = str_replace($key, $value, $errorMessageTemplate);
            }

            $errors[] = sprintf('%s: %s', $e->getOrigin()?->getConfig()->getName(), $errorMessageTemplate);
        }

        return $errors;
    }

    protected function generateFeedStore(): array
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

    protected function translate(?string $value): string
    {
        if (empty($value)) {
            return $value;
        }

        return $this->translator->trans($value, [], 'admin');
    }

    protected function getExtJSSerializerContext(): array
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
                    }

                    if ($data instanceof ConnectorEngineConfigurationInterface) {
                        return $this->serializer->normalize($data);
                    }

                    return [];
                }
            ]
        ];
    }
}
