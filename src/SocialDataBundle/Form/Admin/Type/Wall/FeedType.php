<?php

namespace SocialDataBundle\Form\Admin\Type\Wall;

use SocialDataBundle\Connector\ConnectorDefinitionInterface;
use SocialDataBundle\Connector\ConnectorFeedConfigurationInterface;
use SocialDataBundle\Form\Admin\Type\Wall\Component\ConnectorEngineChoiceType;
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
    /**
     * @var ConnectorManagerInterface
     */
    protected $connectorManager;

    /**
     * @param ConnectorManagerInterface $connectorManager
     */
    public function __construct(ConnectorManagerInterface $connectorManager)
    {
        $this->connectorManager = $connectorManager;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('connectorEngine', ConnectorEngineChoiceType::class, []);
        $builder->add('persistMedia', CheckboxType::class, []);
        $builder->add('publishPostImmediately', CheckboxType::class, []);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {

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

            /** @var ConnectorFeedConfigurationInterface $class */
            $class = $connectorDefinition->getFeedConfigurationClass();

            $form->add('configuration', $class::getFormClass());
        });
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Feed::class
        ]);
    }
}
