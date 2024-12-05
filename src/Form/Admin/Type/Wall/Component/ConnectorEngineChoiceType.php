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

namespace SocialDataBundle\Form\Admin\Type\Wall\Component;

use SocialDataBundle\Manager\ConnectorManagerInterface;
use SocialDataBundle\Registry\ConnectorDefinitionRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConnectorEngineChoiceType extends AbstractType
{
    public function __construct(
        protected ConnectorDefinitionRegistry $connectorDefinitionRegistry,
        protected ConnectorManagerInterface $connectorManager
    ) {
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
