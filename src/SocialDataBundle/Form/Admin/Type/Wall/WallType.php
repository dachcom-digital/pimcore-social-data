<?php

namespace SocialDataBundle\Form\Admin\Type\Wall;

use Doctrine\ORM\EntityManagerInterface;
use SocialDataBundle\Form\Admin\Type\TagCollectionType;
use SocialDataBundle\Form\Admin\Type\Wall\Component\PimcoreRelationType;
use SocialDataBundle\Form\Traits\ExtJsTagTransformTrait;
use SocialDataBundle\Model\Wall;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WallType extends AbstractType
{
    use ExtJsTagTransformTrait;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class);
        $builder->add('wallTags', TagCollectionType::class, ['tag_type' => 'wallTag']);
        $builder->add('dataStorage', PimcoreRelationType::class);
        $builder->add('assetStorage', PimcoreRelationType::class);
        $builder->add('feeds', FeedCollectionType::class);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'adjustFeedsExtJsSubmissionData']);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'adjustTagsExtJsSubmissionData']);
    }

    /**
     * @param FormEvent $event
     */
    public function adjustFeedsExtJsSubmissionData(FormEvent $event)
    {
        $data = $event->getData();

        if (!isset($data['feeds']) || !is_array($data['feeds'])) {
            return;
        }

        $sortedData = [];
        foreach ($data['feeds'] as $feedRow) {

            $id = empty($feedRow['id']) ? null : (int) $feedRow['id'];

            unset($feedRow['id']);

            if ($id === null) {
                $id = md5(uniqid(rand(), true));
            }

            $sortedData[$id] = $feedRow;
        }

        $data['feeds'] = $sortedData;

        $event->setData($data);
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class'      => Wall::class
        ]);
    }
}
