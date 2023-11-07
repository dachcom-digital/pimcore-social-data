<?php

namespace SocialDataBundle\Form\Admin\Type\Wall;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use SocialDataBundle\Model\FeedInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeedCollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // we need to ensure that we have a id-based array
        // @see: https://github.com/symfony/symfony/issues/7828#issuecomment-579608260

        $builder->addModelTransformer(
            new CallbackTransformer(
                function ($feeds) {

                    if (!$feeds instanceof PersistentCollection) {
                        return $feeds;
                    }

                    $feedCollection = new ArrayCollection();
                    /** @var FeedInterface $collectionItem */
                    foreach ($feeds as $collectionItem) {
                        $feedCollection->set($collectionItem->getId(), $collectionItem);
                    }

                    return $feedCollection;
                },
                function ($feeds) {
                    return $feeds;
                }
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'allow_add'    => true,
            'allow_delete' => true,
            'by_reference' => false,
            'entry_type'   => FeedType::class
        ]);
    }

    public function getParent(): string
    {
        return CollectionType::class;
    }
}
