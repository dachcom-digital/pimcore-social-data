<?php

namespace SocialDataBundle\Form\Admin\Type\Wall\Component;

use SocialDataBundle\Manager\ConnectorManagerInterface;
use SocialDataBundle\Registry\ConnectorDefinitionRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConnectorEngineChoiceType extends AbstractType
{
    protected ConnectorDefinitionRegistry $connectorDefinitionRegistry;
    protected ConnectorManagerInterface $connectorManager;

    public function __construct(
        ConnectorDefinitionRegistry $connectorDefinitionRegistry,
        ConnectorManagerInterface $connectorManager
    ) {
        $this->connectorDefinitionRegistry = $connectorDefinitionRegistry;
        $this->connectorManager = $connectorManager;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'choice_value'              => 'id',
                'choice_label'              => 'name',
                'choice_translation_domain' => false,
                'active'                    => true,
                'choices'                   => function (Options $options) {

                    $connectorEngines = [];
                    foreach ($this->connectorManager->getAllActiveConnectorDefinitions() as $connectorDefinition) {
                        $connectorEngines[] = $connectorDefinition->getConnectorEngine();
                    }

                    return $connectorEngines;
                },
            ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
