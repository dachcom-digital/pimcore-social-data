<?php

namespace SocialDataBundle\Form\Admin\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use SocialDataBundle\Model\TagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagCollectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // we need to ensure that we have a id-based array
        // @see: https://github.com/symfony/symfony/issues/7828#issuecomment-579608260

        $builder->addModelTransformer(
            new CallbackTransformer(
                function ($tags) {

                    if (!$tags instanceof PersistentCollection) {
                        return $tags;
                    }

                    $tagCollection = new ArrayCollection();
                    /** @var TagInterface $collectionItem */
                    foreach ($tags as $collectionItem) {
                        $tagCollection->set($collectionItem->getId(), $collectionItem);
                    }

                    return $tagCollection;
                },
                function ($tags) {
                    return $tags;
                }
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'by_reference' => false,
            'allow_add'    => true,
            'allow_delete' => true,
            'tag_type'     => 'wallTag',
            'entry_type'   => TagType::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return CollectionType::class;
    }
}
