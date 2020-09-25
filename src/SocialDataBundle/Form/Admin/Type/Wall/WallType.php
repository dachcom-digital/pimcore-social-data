<?php

namespace SocialDataBundle\Form\Admin\Type\Wall;

use Doctrine\Common\Collections\ArrayCollection;
use SocialDataBundle\Form\Admin\Type\Wall\Component\PimcoreRelationType;
use SocialDataBundle\Model\Wall;
use SocialDataBundle\Model\WallInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WallType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class);
        $builder->add('dataStorage', PimcoreRelationType::class);
        $builder->add('assetStorage', PimcoreRelationType::class);

        $entity = $builder->getData();

        // we need to ensure that we have a id-based array
        // @see: https://github.com/symfony/symfony/issues/7828#issuecomment-579608260

        if ($entity instanceof WallInterface) {
            $indexedCollection = new ArrayCollection();
            foreach ($entity->getFeeds() as $collectionItem) {
                $indexedCollection->set($collectionItem->getId(), $collectionItem);
            }

            $builder->add('feeds', FeedCollectionType::class, ['data' => $indexedCollection]);
        }

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {

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
        });
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
