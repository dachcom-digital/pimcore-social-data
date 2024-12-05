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

namespace SocialDataBundle\Form\Admin\Type\Wall;

use Doctrine\ORM\EntityManagerInterface;
use SocialDataBundle\Connector\ConnectorDefinitionInterface;
use SocialDataBundle\Connector\ConnectorFeedConfigurationInterface;
use SocialDataBundle\Form\Admin\Type\TagCollectionType;
use SocialDataBundle\Form\Admin\Type\Wall\Component\ConnectorEngineChoiceType;
use SocialDataBundle\Form\Traits\ExtJsTagTransformTrait;
use SocialDataBundle\Manager\ConnectorManagerInterface;
use SocialDataBundle\Model\ConnectorEngineInterface;
use SocialDataBundle\Model\Feed;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeedType extends AbstractType
{
    use ExtJsTagTransformTrait;

    public function __construct(
        protected ConnectorManagerInterface $connectorManager,
        protected EntityManagerInterface $entityManager
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('feedTags', TagCollectionType::class, ['tag_type' => 'feedTag']);
        $builder->add('connectorEngine', ConnectorEngineChoiceType::class, []);
        $builder->add('persistMedia', CheckboxType::class, []);
        $builder->add('publishPostImmediately', CheckboxType::class, []);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'adjustTagsExtJsSubmissionData']);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'addConfigurationField']);
    }

    public function addConfigurationField(FormEvent $event): void
    {
        $data = $event->getData();
        $form = $event->getForm();
        $formData = $form->getData();

        // reset old form data to avoid merging old channel data.
        if ($formData instanceof Feed) {
            $formData->setConfiguration(null);
            $form->setData($formData);
        }

        if (!isset($data['connectorEngine'])) {
            throw new InvalidConfigurationException('Value "connectorEngine" not found in FeedType form.');
        }

        $connectorEngine = $this->connectorManager->getEngineById($data['connectorEngine']);
        if (!$connectorEngine instanceof ConnectorEngineInterface) {
            throw new InvalidConfigurationException(sprintf('No Engine found for id %d', $data['connectorEngine']));
        }

        $connectorDefinition = $this->connectorManager->getConnectorDefinition($connectorEngine->getName());
        if (!$connectorDefinition instanceof ConnectorDefinitionInterface) {
            throw new InvalidConfigurationException(sprintf('No Connector definition found for engine "%s"', $connectorEngine->getName()));
        }

        $formType = '';
        $class = $connectorDefinition->getFeedConfigurationClass();
        if (is_subclass_of($class, ConnectorFeedConfigurationInterface::class)) {
            $formType = $class::getFormClass();
        }

        $form->add('configuration', $formType);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Feed::class
        ]);
    }
}
